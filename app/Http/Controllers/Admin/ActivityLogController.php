<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user')->orderByDesc('created_at');

        if ($action = $request->input('action')) {
            $query->where('action', $action);
        }

        $logs = $query->paginate(30)->withQueryString();

        $actions = ActivityLog::select('action')
                               ->distinct()
                               ->orderBy('action')
                               ->pluck('action');

        return view('admin.activity-logs.index', compact('logs', 'actions'));
    }
}
