<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Blog;
use App\Models\Download;
use App\Models\Event;
use App\Models\GalleryFolder;
use App\Models\Inquiry;
use App\Models\Member;

class DashboardController extends Controller
{
    public function index()
    {
        // Teacher-only accounts land on their own classes page.
        if (auth()->user()->isTeacherOnly()) {
            return redirect()->route('admin.classes.index');
        }

        $stats = [
            'active_students' => Member::where('is_active', true)->count(),
            'total_students'  => Member::count(),
            'news'            => Blog::where('published', true)->count(),
            'gallery'         => GalleryFolder::count(),
            'downloads'       => Download::count(),
            'events'          => Event::count(),
            'upcoming'        => Event::where('starts_at', '>=', now())->count(),
            'new_inquiries'   => Inquiry::where('status', 'new')->count(),
        ];

        // Students per class
        $classCounts = [];
        $rawCounts = Member::selectRaw('class, count(*) as total')
            ->whereNotNull('class')
            ->groupBy('class')
            ->pluck('total', 'class')
            ->toArray();
        foreach (Member::classes() as $class) {
            $classCounts[$class] = $rawCounts[$class] ?? 0;
        }

        // Recent 8 activity logs
        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Recent 5 students
        $recentMembers = Member::orderByDesc('created_at')->take(5)->get();

        // Upcoming events
        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'classCounts', 'recentActivity', 'recentMembers', 'upcomingEvents'
        ));
    }
}
