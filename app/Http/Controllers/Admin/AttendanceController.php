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
        $records    = collect();
        $summary    = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];

        // Daily view data
        if ($view === 'daily' && $year && $class) {
            $query = AttendanceRecord::forActiveYear()
                ->where('class', $class)
                ->when($section, fn ($q) => $q->where('section', $section))
                ->whereDate('date', $date)
                ->with(['enrollment.student', 'marker']);

            $records = $query->get()
                ->sortBy(fn ($r) => [(int) ($r->enrollment->roll_number ?: 999999), $r->enrollment?->student?->name ?? ''])
                ->values();

            foreach ($records as $r) {
                $summary[$r->status] = ($summary[$r->status] ?? 0) + 1;
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

        return view('admin.attendance.index', compact(
            'year', 'slots', 'class', 'section', 'date', 'records', 'summary',
            'view', 'month', 'monthStart', 'monthEnd',
            'studentStats', 'monthlyTrend', 'classAvgPct', 'totalDays'
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
                    ]
                );
                $count++;
            }
        }

        $dayCount = count($dates);
        return back()->with('success', "Attendance marked for {$enrollments->count()} students across {$dayCount} days ({$count} records).");
    }
}
