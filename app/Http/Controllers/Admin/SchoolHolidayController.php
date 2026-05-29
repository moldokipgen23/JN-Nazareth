<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolHoliday;
use Illuminate\Http\Request;

class SchoolHolidayController extends Controller
{
    public function index()
    {
        $holidays = SchoolHoliday::orderBy('date')->with('creator')->get();
        return view('admin.school-holidays.index', compact('holidays'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date'   => 'required|date|unique:school_holidays,date',
            'reason' => 'nullable|string|max:255',
        ]);

        SchoolHoliday::create([
            'date'       => $data['date'],
            'reason'     => $data['reason'] ?? null,
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'Holiday marked for ' . $data['date']);
    }

    public function destroy(SchoolHoliday $schoolHoliday)
    {
        $schoolHoliday->delete();
        return back()->with('success', 'Holiday removed.');
    }
}
