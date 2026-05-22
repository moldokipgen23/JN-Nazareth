<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = Inquiry::query();
        if (in_array($status, ['new', 'read', 'replied'], true)) {
            $query->where('status', $status);
        }

        $inquiries = $query->orderByDesc('created_at')
                           ->paginate(20)
                           ->withQueryString();

        $counts = [
            'all'     => Inquiry::count(),
            'new'     => Inquiry::where('status', 'new')->count(),
            'read'    => Inquiry::where('status', 'read')->count(),
            'replied' => Inquiry::where('status', 'replied')->count(),
        ];

        return view('admin.inquiries.index', compact('inquiries', 'counts', 'status'));
    }

    public function show(Inquiry $inquiry)
    {
        if ($inquiry->status === 'new') {
            $inquiry->update(['status' => 'read']);
        }

        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function markReplied(Inquiry $inquiry)
    {
        $inquiry->update(['status' => 'replied']);

        ActivityLogger::log('inquiry_replied', $inquiry, "Marked inquiry replied: {$inquiry->name}");

        return back()->with('success', 'Inquiry marked as replied.');
    }

    public function destroy(Inquiry $inquiry)
    {
        $name = $inquiry->name;
        $inquiry->delete();

        ActivityLogger::log('inquiry_deleted', null, "Deleted inquiry: {$name}");

        return redirect()->route('admin.inquiries.index')
                         ->with('success', 'Inquiry deleted successfully.');
    }
}
