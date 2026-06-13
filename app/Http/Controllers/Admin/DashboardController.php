<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\AttendanceRecord;
use App\Models\Blog;
use App\Models\Download;
use App\Models\Event;
use App\Models\GalleryFolder;
use App\Models\Inquiry;
use App\Models\ExamQuestion;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\SchoolHoliday;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Teacher-only accounts land on the mobile teacher portal.
        if (auth()->user()->isTeacherOnly()) {
            return redirect()->route('teacher.dashboard');
        }

        $stats = [
            'active_students' => StudentEnrollment::forActiveYear()->active()->count(),
            'total_students'  => Student::count(),
            'news'            => Blog::where('published', true)->count(),
            'gallery'         => GalleryFolder::count(),
            'downloads'       => Download::count(),
            'events'          => Event::count(),
            'upcoming'        => Event::where('starts_at', '>=', now())->count(),
            'new_inquiries'   => Inquiry::where('status', 'new')->count(),
        ];

        // Students per class
        $classCounts = [];
        $rawCounts = StudentEnrollment::forActiveYear()
            ->active()
            ->whereNotNull('class')
            ->groupBy('class')
            ->selectRaw('class, count(*) as total')
            ->pluck('total', 'class')
            ->toArray();
        foreach (Student::classes() as $class) {
            $classCounts[$class] = $rawCounts[$class] ?? 0;
        }

        // Recent 8 activity logs
        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Recent 5 students
        $recentStudents = Student::orderByDesc('created_at')->take(5)->get();

        // Upcoming events
        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        // ── New widget data ──
        $year = AcademicYear::current();

        // Today's attendance completion
        $today = now()->toDateString();
        $todayExpected = $year ? StudentEnrollment::forActiveYear()->active()->count() : 0;
        $todayMarked   = $year ? AttendanceRecord::forActiveYear()->whereDate('date', $today)->distinct('student_enrollment_id')->count('student_enrollment_id') : 0;
        $todayPct      = $todayExpected > 0 ? round(($todayMarked / $todayExpected) * 100) : 0;

        // Pending reviews
        $pendingQuestions = $year ? ExamQuestion::where('academic_year_id', $year->id)->where('status', 'pending')->count() : 0;
        $pendingNotes     = 0;
        $pendingMarks     = $year
            ? \App\Models\Mark::where('academic_year_id', $year->id)
                ->whereNotNull('submitted_at')->whereNull('approved_at')->count()
            : 0;
        $pendingAttendance = $year
            ? AttendanceRecord::forActiveYear()->where('approval_status', 'pending')->count()
            : 0;

        // Marks distribution (basic)
        $totalMarked  = 0;
        $passMarked   = 0;
        if ($year) {
            $yearId = $year->id;
            // Pass rate counts only APPROVED marks. Pending or rejected rows
            // would otherwise inflate the school-wide stat with unvetted data.
            $allMarks = \App\Models\Mark::where('academic_year_id', $yearId)
                ->whereNotNull('approved_at')
                ->selectRaw('count(*) as total, SUM(CASE WHEN total_marks >= pass_marks THEN 1 ELSE 0 END) as passed')
                ->first();
            $totalMarked = $allMarks?->total ?? 0;
            $passMarked  = $allMarks?->passed ?? 0;
        }
        $marksPassPct = $totalMarked > 0 ? round(($passMarked / $totalMarked) * 100) : 0;

        // Classes that haven't marked attendance today — daily-marking enforcement.
        // Only shown on school days (skip weekends + scheduled holidays).
        $missingAttendanceClasses = collect();
        $isSchoolDayToday = false;
        $missedBacklog = collect();
        if ($year) {
            $todayCarbon = Carbon::today();
            $isWeekend   = $todayCarbon->isSaturday() || $todayCarbon->isSunday();
            $isHoliday   = SchoolHoliday::whereDate('date', $today)->exists();
            $isSchoolDayToday = !$isWeekend && !$isHoliday;

            // All active class+section combos for this year
            $allSlots = StudentEnrollment::forActiveYear()->active()
                ->select('class', 'section')
                ->groupBy('class', 'section')
                ->selectRaw('COUNT(*) as student_count')
                ->get();

            $classOrder = array_flip(Student::classes());

            if ($isSchoolDayToday) {
                // Today's gap (shown as the primary nag)
                $markedSlotsToday = AttendanceRecord::forActiveYear()
                    ->whereDate('date', $today)
                    ->select('class', 'section')
                    ->distinct()
                    ->get()
                    ->map(fn ($r) => $r->class.'|'.$r->section)
                    ->all();

                $missingAttendanceClasses = $allSlots
                    ->filter(fn ($s) => ! in_array($s->class.'|'.$s->section, $markedSlotsToday, true))
                    ->sortBy(fn ($s) => [$classOrder[$s->class] ?? 999, $s->section])
                    ->values();
            }

            // Backlog: scan the last 7 school days (excluding today + weekends
            // + holidays). Surfaces past misses so admin can backfill them.
            $holidaySet = SchoolHoliday::whereBetween('date', [$todayCarbon->copy()->subDays(14), $todayCarbon])
                ->pluck('date')->map(fn ($d) => Carbon::parse($d)->toDateString())->all();

            $schoolDays = [];
            $cursor = $todayCarbon->copy()->subDay();
            while (count($schoolDays) < 7 && $cursor->gte($todayCarbon->copy()->subDays(14))) {
                $ds = $cursor->toDateString();
                if (!$cursor->isSaturday() && !$cursor->isSunday() && !in_array($ds, $holidaySet, true)) {
                    $schoolDays[] = $ds;
                }
                $cursor->subDay();
            }

            if (!empty($schoolDays)) {
                $markedByDate = AttendanceRecord::forActiveYear()
                    ->whereIn('date', $schoolDays)
                    ->select('class', 'section', 'date')
                    ->distinct()
                    ->get()
                    ->groupBy(fn ($r) => Carbon::parse($r->date)->toDateString())
                    ->map(fn ($rows) => $rows->map(fn ($r) => $r->class.'|'.$r->section)->all())
                    ->all();

                $backlogRows = [];
                foreach ($allSlots as $s) {
                    $key = $s->class.'|'.$s->section;
                    $missed = [];
                    foreach ($schoolDays as $ds) {
                        if (!in_array($key, $markedByDate[$ds] ?? [], true)) {
                            $missed[] = $ds;
                        }
                    }
                    if (!empty($missed)) {
                        $backlogRows[] = (object) [
                            'class'     => $s->class,
                            'section'   => $s->section,
                            'missed'    => $missed,
                            'count'     => count($missed),
                        ];
                    }
                }
                $missedBacklog = collect($backlogRows)
                    ->sortBy(fn ($r) => [$classOrder[$r->class] ?? 999, $r->section])
                    ->values();
            }
        }

        return view('admin.dashboard', compact(
            'stats', 'classCounts', 'recentActivity', 'recentStudents', 'upcomingEvents',
            'todayMarked', 'todayExpected', 'todayPct',
            'pendingQuestions', 'pendingNotes', 'pendingMarks', 'pendingAttendance',
            'totalMarked', 'passMarked', 'marksPassPct',
            'missingAttendanceClasses', 'isSchoolDayToday', 'missedBacklog'
        ));
    }
}
