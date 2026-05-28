<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class WorkingYearController extends Controller
{
    public function switch(Request $request)
    {
        $year = AcademicYear::findOrFail($request->year_id);
        session(['working_year_id' => $year->id]);
        return back()->with('success', 'Working year switched to ' . $year->name);
    }
}
