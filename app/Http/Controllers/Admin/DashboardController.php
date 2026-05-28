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
use App\Models\Question;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\TeacherNote;

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
        $pendingQuestions = $year ? Question::forActiveYear()->pending()->count() : 0;
        $pendingNotes     = $year ? TeacherNote::forActiveYear()->pending()->count() : 0;

        // Marks distribution (basic)
        $totalMarked  = 0;
        $passMarked   = 0;
        if ($year) {
            $yearId = $year->id;
            $allMarks = \App\Models\Mark::where('academic_year_id', $yearId)
                ->selectRaw('count(*) as total, SUM(CASE WHEN total_marks >= pass_marks THEN 1 ELSE 0 END) as passed')
                ->first();
            $totalMarked = $allMarks?->total ?? 0;
            $passMarked  = $allMarks?->passed ?? 0;
        }
        $marksPassPct = $totalMarked > 0 ? round(($passMarked / $totalMarked) * 100) : 0;

        return view('admin.dashboard', compact(
            'stats', 'classCounts', 'recentActivity', 'recentStudents', 'upcomingEvents',
            'todayMarked', 'todayExpected', 'todayPct',
            'pendingQuestions', 'pendingNotes',
            'totalMarked', 'passMarked', 'marksPassPct'
        ));
    }
}
