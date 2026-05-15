<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Blog;
use App\Models\Document;
use App\Models\Event;
use App\Models\Folder;
use App\Models\GalleryItem;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'members'    => Member::count(),
            'blogs'      => Blog::count(),
            'events'     => Event::count(),
            'documents'  => Document::count(),
            'folders'    => Folder::count(),
            'gallery'    => GalleryItem::count(),
            'users'      => User::count(),
            'published'  => Blog::where('published', true)->count(),
            'upcoming'   => Event::where('starts_at', '>=', now())->count(),
        ];

        // Members added per month (last 6 months) — compatible with MySQL and SQLite
        $dbDriver = config('database.default');
        $monthExpr = $dbDriver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $memberGrowth = Member::selectRaw("{$monthExpr} as month, count(*) as total")
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Build 6-month label array
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $months[$key] = $memberGrowth[$key] ?? 0;
        }

        // Recent 8 activity logs
        $recentActivity = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->take(8)
            ->get();

        // Recent 5 members
        $recentMembers = Member::orderByDesc('created_at')->take(5)->get();

        // Upcoming events
        $upcomingEvents = Event::where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(4)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 'months', 'recentActivity', 'recentMembers', 'upcomingEvents'
        ));
    }
}
