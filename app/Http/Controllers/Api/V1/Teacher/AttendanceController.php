<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\ClassTeacherAssignment;
use App\Models\SchoolHoliday;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = AcademicYear::current();

        $slots = collect();

        if ($year) {
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
        $today = Carbon::today()->toDateString();

        $rows = $slots->map(function ($s) use ($year, $today) {
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

        return response()->json([
            'success' => true,
            'data' => [
                'rows' => $rows,
                'date' => $today,
            ],
        ]);
    }

    public function markForm(Request $request): JsonResponse
    {
        $year = $this->requireActiveYear();
        $class   = $request->query('class');
        $section = $request->query('section');

        if (! $class || ! $section) {
            return response()->json(['success' => false, 'message' => 'class and section are required.'], 422);
        }

        $this->authorizeClassAccess($request, $class, $section);

        $date = Carbon::today()->toDateString();
        $isHoliday = SchoolHoliday::whereDate('date', $date)->exists();

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $class)->where('section', $section)
            ->with('student')
            ->get()
            ->sortBy(fn ($e) => [(int) $e->roll_number ?: 999999, $e->student?->name ?? ''])
            ->values()
            ->map(fn ($e) => [
                'id'           => $e->id,
                'roll_number'  => $e->roll_number,
                'student_name' => $e->student?->name ?? '',
                'father_name'  => $e->student?->father_name ?? '',
                'parent_phone' => $e->student?->parent_phone ?? '',
            ]);

        $existing = AttendanceRecord::forActiveYear()
            ->where('class', $class)->where('section', $section)
            ->whereDate('date', $date)
            ->get()
            ->keyBy('student_enrollment_id')
            ->map(fn ($r) => [
                'status'  => $r->status,
                'remarks' => $r->remarks,
            ]);

        $isSubmitted = $existing->isNotEmpty();

        return response()->json([
            'success' => true,
            'data' => [
                'class'       => $class,
                'section'     => $section,
                'date'        => $date,
                'enrollments' => $enrollments,
                'existing'    => $existing,
                'statuses'    => AttendanceRecord::STATUSES,
                'isHoliday'   => $isHoliday,
                'isSubmitted' => $isSubmitted,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $year = $this->requireActiveYear();

        $data = $request->validate([
            'class'              => 'required|string',
            'section'            => 'required|string',
            'date'               => 'required|date',
            'marks'              => 'required|array',
            'marks.*.enrollment_id' => 'required|integer',
            'marks.*.status'     => 'required|in:' . implode(',', AttendanceRecord::STATUSES),
            'marks.*.remarks'    => 'nullable|string|max:500',
        ]);

        $this->authorizeClassAccess($request, $data['class'], $data['section']);

        $today = Carbon::today()->toDateString();
        if ($data['date'] !== $today) {
            return response()->json(['success' => false, 'message' => 'Only today\'s date is allowed.'], 422);
        }

        if (SchoolHoliday::whereDate('date', $today)->exists()) {
            return response()->json(['success' => false, 'message' => 'Today is a school holiday.'], 403);
        }

        $already = AttendanceRecord::forActiveYear()
            ->where('class', $data['class'])->where('section', $data['section'])
            ->whereDate('date', $today)
            ->exists();

        if ($already) {
            return response()->json(['success' => false, 'message' => 'Attendance already submitted for today.'], 409);
        }

        $enrollmentIds = StudentEnrollment::forActiveYear()->active()
            ->where('class', $data['class'])->where('section', $data['section'])
            ->pluck('id')->all();

        $saved = 0;
        foreach ($data['marks'] as $row) {
            $eid = (int) $row['enrollment_id'];
            if (! in_array($eid, $enrollmentIds, true)) continue;

            AttendanceRecord::create([
                'academic_year_id'      => $year->id,
                'student_enrollment_id' => $eid,
                'class'                 => $data['class'],
                'section'               => $data['section'],
                'date'                  => $today,
                'status'                => $row['status'],
                'marked_by'             => $request->user()->id,
                'remarks'               => $row['remarks'] ?? null,
                'approval_status'       => AttendanceRecord::APPROVAL_PENDING,
            ]);
            $saved++;
        }

        return response()->json([
            'success' => true,
            'message' => "Attendance saved for {$saved} student" . ($saved === 1 ? '' : 's') . '.',
            'data'    => ['saved' => $saved],
        ]);
    }

    protected function requireActiveYear(): AcademicYear
    {
        $year = AcademicYear::current();
        if (! $year) {
            abort(409, 'No active academic year.');
        }
        return $year;
    }

    protected function authorizeClassAccess(Request $request, string $class, string $section): void
    {
        $user = $request->user();
        if ($user->isAdmin()) return;
        if (! $user->isClassTeacherFor($class, $section)) {
            abort(403, 'You are not the class teacher for this section.');
        }
    }
}
