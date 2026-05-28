<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassTeacherAssignment;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AttendanceController extends Controller
{
    /** List the classes the current user can mark attendance for. */
    public function index()
    {
        $user = auth()->user();
        $year = AcademicYear::current();

        $slots = collect();

        if ($year) {
            // Admin sees every class/section that has enrollments.
            if ($user->isAdmin()) {
                $slots = StudentEnrollment::forActiveYear()->active()
                    ->select('class', 'section')
                    ->groupBy('class', 'section')
                    ->get();
            } else {
                $slots = ClassTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                    ->where('academic_year_id', $year->id)
                    ->get(['class', 'section']);
            }
        }

        $order = array_flip(Student::classes());
        $rows  = $slots->map(function ($s) use ($year) {
            $today = Carbon::today()->toDateString();
            return [
                'class'         => $s->class,
                'section'       => $s->section,
                'students'      => $year
                    ? StudentEnrollment::forActiveYear()->active()
                        ->where('class', $s->class)->where('section', $s->section)
                        ->count()
                    : 0,
                'marked_today'  => $year
                    ? AttendanceRecord::forActiveYear()
                        ->where('class', $s->class)->where('section', $s->section)
                        ->whereDate('date', $today)
                        ->count()
                    : 0,
            ];
        })->sortBy(fn ($r) => [$order[$r['class']] ?? 999, $r['section']])->values();

        return view('teacher.attendance.index', [
            'rows' => $rows,
            'year' => $year,
        ]);
    }

    /** Show the roster for a class/section + date with current marks. */
    public function mark(Request $request, string $class, string $section)
    {
        $year = $this->requireActiveYear();
        $this->authorizeClassAccess($class, $section);

        $date = $request->query('date')
            ? Carbon::parse($request->query('date'))->toDateString()
            : Carbon::today()->toDateString();

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->with('student')
            ->get()
            ->sortBy(fn ($e) => [(int) $e->roll_number ?: 999999, $e->student?->name ?? ''])
            ->values();

        $existing = AttendanceRecord::forActiveYear()
            ->where('class', $class)->where('section', $section)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_enrollment_id');

        return view('teacher.attendance.mark', [
            'year'        => $year,
            'class'       => $class,
            'section'     => $section,
            'date'        => $date,
            'enrollments' => $enrollments,
            'existing'    => $existing,
            'statuses'    => AttendanceRecord::STATUSES,
        ]);
    }

    /** Save bulk attendance for one class/section/date. */
    public function store(Request $request, string $class, string $section)
    {
        $year = $this->requireActiveYear();
        $this->authorizeClassAccess($class, $section);

        $data = $request->validate([
            'date'              => 'required|date',
            'marks'             => 'required|array',
            'marks.*.status'    => 'required|in:'.implode(',', AttendanceRecord::STATUSES),
            'marks.*.remarks'   => 'nullable|string|max:500',
        ]);

        $date = Carbon::parse($data['date'])->toDateString();

        $enrollmentIds = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->pluck('id')
            ->all();

        $saved = 0;
        foreach ($data['marks'] as $enrollmentId => $row) {
            $enrollmentId = (int) $enrollmentId;
            if (! in_array($enrollmentId, $enrollmentIds, true)) {
                continue; // ignore foreign rows
            }
            AttendanceRecord::updateOrCreate(
                ['student_enrollment_id' => $enrollmentId, 'date' => $date],
                [
                    'academic_year_id' => $year->id,
                    'class'            => $class,
                    'section'          => $section,
                    'status'           => $row['status'],
                    'marked_by'        => auth()->id(),
                    'remarks'          => $row['remarks'] ?? null,
                ]
            );
            $saved++;
        }

        return redirect()
            ->route('teacher.attendance.mark', ['class' => $class, 'section' => $section, 'date' => $date])
            ->with('success', "Attendance saved for {$saved} student".($saved === 1 ? '' : 's').'.');
    }

    // ────────────────────────────────────────────────────────────────────

    protected function requireActiveYear(): AcademicYear
    {
        $year = AcademicYear::current();
        if (! $year) {
            throw new HttpException(409, 'No active academic year is set.');
        }
        return $year;
    }

    /** Admins pass; teachers must be the class-teacher for this class+section. */
    protected function authorizeClassAccess(string $class, string $section): void
    {
        $user = auth()->user();
        if ($user->isAdmin()) {
            return;
        }
        if (! $user->isClassTeacherFor($class, $section)) {
            abort(403, 'You are not the class teacher for this section.');
        }
    }
}
