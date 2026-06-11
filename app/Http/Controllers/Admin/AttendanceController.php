<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\Student;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::current();
        $view = $request->query('view', 'daily');

        $class   = $request->query('class');
        $section = $request->query('section');

        $slots = $year
            ? StudentEnrollment::forActiveYear()->active()
                ->select('class', 'section')->groupBy('class', 'section')->get()
            : collect();

        $order = array_flip(Student::classes());
        $slots = $slots->sortBy(fn ($s) => [$order[$s->class] ?? 999, $s->section])->values();

        $date       = $request->query('date', Carbon::today()->toDateString());
        $month      = $request->query('month', now()->format('Y-m'));
        $approvalStatus = $request->query('approval_status', '');
        $records    = collect();
        $summary    = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];

        // Daily view data
        $backfillEnrollments = collect();
        $canBackfill = false;
        if ($view === 'daily' && $year && $class) {
            $query = AttendanceRecord::forActiveYear()
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->whereDate('date', $date)
                ->when($approvalStatus, fn ($q) => $q->where('approval_status', $approvalStatus))
                ->with(['enrollment.student', 'marker', 'approver']);

            $records = $query->get()
                ->sortBy(fn ($r) => [(int) ($r->enrollment->roll_number ?: 999999), $r->enrollment?->student?->name ?? ''])
                ->values();

            foreach ($records as $r) {
                $summary[$r->status] = ($summary[$r->status] ?? 0) + 1;
            }

            // If no records exist for this date AND the date is not a holiday,
            // load enrollments so admin can backfill via the in-page form.
            if ($records->isEmpty() && $section) {
                $isHoliday = \App\Models\SchoolHoliday::whereDate('date', $date)->exists();
                if (!$isHoliday) {
                    $backfillEnrollments = StudentEnrollment::forActiveYear()->active()
                        ->where('class', $class)
                        ->where('section', $section)
                        ->with('student')
                        ->orderBy('roll_number')
                        ->get();
                    $canBackfill = $backfillEnrollments->isNotEmpty();
                }
            }
        }

        // Analytics data
        $studentStats = collect();
        $monthlyTrend = collect();
        $classAvgPct  = null;
        $totalDays    = 0;
        $monthStart   = Carbon::parse($month.'-01')->startOfMonth();
        $monthEnd     = $monthStart->copy()->endOfMonth();

        if ($year && $class) {
            $enrollments = StudentEnrollment::forActiveYear()->active()
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->with('student')->orderBy('roll_number')->get();

            $allRecords = AttendanceRecord::forActiveYear()
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->where('approval_status', 'approved')
                ->whereBetween('date', [$monthStart, $monthEnd])->get();

            $totalDays = $allRecords->pluck('date')->unique()->sort()->values()->count();
            $recordsByStudent = $allRecords->groupBy('student_enrollment_id');

            foreach ($enrollments as $enrollment) {
                $recs = $recordsByStudent->get($enrollment->id, collect());
                $present = $recs->where('status', 'present')->count();
                $absent  = $recs->where('status', 'absent')->count();
                $late    = $recs->where('status', 'late')->count();
                $excused = $recs->where('status', 'excused')->count();
                $marked  = $present + $absent + $late + $excused;
                $pct     = $marked > 0 ? round(($present + $late + $excused) / $marked * 100, 1) : null;

                $studentStats->push([
                    'enrollment' => $enrollment, 'present' => $present,
                    'absent' => $absent, 'late' => $late, 'excused' => $excused,
                    'marked' => $marked, 'pct' => $pct,
                ]);
            }

            $pcts = $studentStats->pluck('pct')->filter();
            $classAvgPct = $pcts->isNotEmpty() ? round($pcts->avg(), 1) : null;

            $trendStart = $monthStart->copy()->subMonths(5)->startOfMonth();
            $trendRecords = AttendanceRecord::forActiveYear()
                ->where('class', $class)->when($section, fn ($q) => $q->where('section', $section))
                ->where('approval_status', 'approved')
                ->whereBetween('date', [$trendStart, $monthEnd])->get()
                ->groupBy(fn ($r) => $r->date->format('Y-m'));

            $cursor = $trendStart->copy();
            while ($cursor->lte($monthEnd)) {
                $key = $cursor->format('Y-m');
                $mr = $trendRecords->get($key, collect());
                $mp = $mr->where('status', 'present')->count();
                $ma = $mr->where('status', 'absent')->count();
                $ml = $mr->where('status', 'late')->count();
                $me = $mr->where('status', 'excused')->count();
                $mt = $mp + $ma + $ml + $me;
                $monthlyTrend->push([
                    'label' => $cursor->format('M Y'), 'month' => $key,
                    'present' => $mp, 'absent' => $ma, 'late' => $ml, 'excused' => $me,
                    'total' => $mt, 'pct' => $mt > 0 ? round(($mp + $ml + $me) / $mt * 100, 1) : null,
                ]);
                $cursor->addMonth();
            }
        }

        $pendingCount = $year
            ? AttendanceRecord::forActiveYear()->where('approval_status', 'pending')->count()
            : 0;

        // Global pending inbox — grouped by class/section/date/marker so admin can see WHO submitted WHAT
        $pendingInbox = collect();
        if ($year && $pendingCount > 0) {
            $pendingInbox = AttendanceRecord::forActiveYear()
                ->where('approval_status', 'pending')
                ->with('marker:id,name')
                ->select('class', 'section', 'date', 'marked_by')
                ->selectRaw('COUNT(*) as student_count')
                ->selectRaw('MIN(created_at) as first_marked_at')
                ->groupBy('class', 'section', 'date', 'marked_by')
                ->orderByDesc('date')
                ->orderBy('class')
                ->orderBy('section')
                ->limit(50)
                ->get();
        }

        return view('admin.attendance.index', compact(
            'year', 'class', 'section', 'date', 'view', 'slots', 'month',
            'records', 'summary', 'approvalStatus', 'pendingCount', 'pendingInbox',
            'studentStats', 'monthlyTrend', 'classAvgPct', 'monthStart', 'monthEnd', 'totalDays',
            'backfillEnrollments', 'canBackfill',
        ));
    }

    public function update(Request $request, AttendanceRecord $attendance)
    {
        $data = $request->validate([
            'status'  => 'required|in:'.implode(',', AttendanceRecord::STATUSES),
            'remarks' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status'    => $data['status'],
            'remarks'   => $data['remarks'] ?? null,
            'marked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Attendance updated.');
    }

    public function analytics(Request $request)
    {
        return redirect()->route('admin.attendance.index', array_merge($request->query(), ['view' => 'analytics']));
    }

    public function exportCsv(Request $request)
    {
        $year = AcademicYear::current();
        $class   = $request->query('class');
        $section = $request->query('section');
        $date    = $request->query('date', Carbon::today()->toDateString());

        if (!$year || !$class) {
            return back()->with('error', 'Select a class to export.');
        }

        $records = AttendanceRecord::forActiveYear()
            ->where('class', $class)
            ->when($section, fn ($q) => $q->where('section', $section))
            ->whereDate('date', $date)
            ->with(['enrollment.student', 'marker'])
            ->get()
            ->sortBy(fn ($r) => [(int) ($r->enrollment->roll_number ?: 999999), $r->enrollment?->student?->name ?? '']);

        $csv = "Roll No,Student Name,Class,Section,Date,Status,Marked By\n";
        foreach ($records as $r) {
            $csv .= implode(',', [
                $r->enrollment->roll_number ?? '',
                '"'.str_replace('"', '""', $r->enrollment->student?->name ?? '').'"',
                $r->class,
                $r->section,
                $r->date,
                $r->status,
                '"'.str_replace('"', '""', $r->marker?->name ?? '').'"',
            ])."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="attendance-'.$class.'-'.$date.'.csv"',
        ]);
    }

    public function approveDay(Request $request)
    {
        $data = $request->validate([
            'class'   => 'required|string',
            'section' => 'nullable|string',
            'date'    => 'required|date',
        ]);

        AttendanceRecord::forActiveYear()
            ->where('class', $data['class'])
            ->when($data['section'] ?? null, fn ($q) => $q->where('section', $data['section']))
            ->whereDate('date', $data['date'])
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by'     => auth()->id(),
                'approved_at'     => now(),
            ]);

        return back()->with('success', 'Attendance approved for ' . $data['class'] . ' on ' . $data['date']);
    }

    public function rejectDay(Request $request)
    {
        $data = $request->validate([
            'class'   => 'required|string',
            'section' => 'nullable|string',
            'date'    => 'required|date',
        ]);

        AttendanceRecord::forActiveYear()
            ->where('class', $data['class'])
            ->when($data['section'] ?? null, fn ($q) => $q->where('section', $data['section']))
            ->whereDate('date', $data['date'])
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'rejected',
                'approved_by'     => auth()->id(),
                'approved_at'     => now(),
            ]);

        return back()->with('success', 'Attendance rejected for ' . $data['class'] . ' on ' . $data['date']);
    }

    public function approveAllPending(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409);

        $count = AttendanceRecord::forActiveYear()
            ->where('approval_status', 'pending')
            ->update([
                'approval_status' => 'approved',
                'approved_by'     => auth()->id(),
                'approved_at'     => now(),
            ]);

        return back()->with('success', "{$count} pending attendance records approved.");
    }

    /**
     * Admin backfill: mark attendance for a past date when the class teacher
     * forgot. Per-student status, marked as approved immediately because the
     * admin is the authority.
     */
    public function backfillDay(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409);

        $data = $request->validate([
            'class'           => 'required|string',
            'section'         => 'required|string',
            'date'            => 'required|date|before_or_equal:today',
            'marks'           => 'required|array',
            'marks.*.status'  => 'required|in:'.implode(',', AttendanceRecord::STATUSES),
            'marks.*.remarks' => 'nullable|string|max:500',
        ]);

        // Block holiday backfill — protects against accidentally recording on
        // a declared off day.
        if (\App\Models\SchoolHoliday::whereDate('date', $data['date'])->exists()) {
            return redirect()->backFresh()->with('error', 'That date is a school holiday — attendance cannot be backfilled.');
        }

        $enrollmentIds = StudentEnrollment::forActiveYear()->active()
            ->where('class', $data['class'])
            ->where('section', $data['section'])
            ->pluck('id')
            ->all();

        $saved = 0;
        $skipped = 0;
        foreach ($data['marks'] as $enrollmentId => $row) {
            $enrollmentId = (int) $enrollmentId;
            if (!in_array($enrollmentId, $enrollmentIds, true)) continue;

            // firstOrCreate guards against duplicate inserts if admin races with
            // a teacher submission on the same day.
            $rec = AttendanceRecord::firstOrCreate(
                ['student_enrollment_id' => $enrollmentId, 'date' => $data['date']],
                [
                    'academic_year_id' => $year->id,
                    'class'            => $data['class'],
                    'section'          => $data['section'],
                    'status'           => $row['status'],
                    'remarks'          => $row['remarks'] ?? null,
                    'marked_by'        => auth()->id(),
                    'approval_status'  => 'approved',
                    'approved_by'      => auth()->id(),
                    'approved_at'      => now(),
                ]
            );
            $rec->wasRecentlyCreated ? $saved++ : $skipped++;
        }

        $msg = "Backfilled attendance for {$saved} student".($saved === 1 ? '' : 's')." on ".Carbon::parse($data['date'])->format('d M Y').'.';
        if ($skipped > 0) $msg .= " ({$skipped} already had records and were left untouched.)";
        return redirect()->backFresh()->with('success', $msg);
    }

    public function bulkStore(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409);

        $data = $request->validate([
            'class'    => 'required|string',
            'section'  => 'required|string',
            'status'   => 'required|in:'.implode(',', AttendanceRecord::STATUSES),
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $enrollments = StudentEnrollment::forActiveYear()->active()
            ->where('class', $data['class'])
            ->where('section', $data['section'])
            ->pluck('id');

        if ($enrollments->isEmpty()) {
            return back()->with('error', 'No active students found.');
        }

        $start = Carbon::parse($data['start_date']);
        $end   = Carbon::parse($data['end_date']);
        $count = 0;

        $dates = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $dates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        foreach ($enrollments as $enrollmentId) {
            foreach ($dates as $date) {
                AttendanceRecord::firstOrCreate(
                    ['student_enrollment_id' => $enrollmentId, 'date' => $date],
                    [
                        'academic_year_id' => $year->id,
                        'class'            => $data['class'],
                        'section'          => $data['section'],
                        'status'           => $data['status'],
                        'marked_by'        => auth()->id(),
                        'approval_status'  => 'approved',
                        'approved_by'      => auth()->id(),
                        'approved_at'      => now(),
                    ]
                );
                $count++;
            }
        }

        $dayCount = count($dates);
        return back()->with('success', "Attendance marked for {$enrollments->count()} students across {$dayCount} days ({$count} records).");
    }
}
