<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DivisionRule;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GradeScaleController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'grades');
        $grades = GradeScale::orderBy('min_percent')->orderBy('name')->get();
        $divisions = DivisionRule::orderBy('min_percent')->get();
        return view('admin.grade-scales.index', compact('grades', 'divisions', 'tab'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:10|unique:grade_scales,name',
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gte:min_percent',
            'grade_point' => 'required|numeric|min:0|max:10',
        ]);

        GradeScale::create($data);

        return back()->with('success', "Grade {$data['name']} added.");
    }

    public function update(Request $request, GradeScale $gradeScale)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:10', Rule::unique('grade_scales', 'name')->ignore($gradeScale->id)],
            'min_percent' => 'required|numeric|min:0|max:100',
            'max_percent' => 'required|numeric|min:0|max:100|gte:min_percent',
            'grade_point' => 'required|numeric|min:0|max:10',
        ]);

        $gradeScale->update($data);

        return back()->with('success', 'Grade updated.');
    }

    public function destroy(GradeScale $gradeScale)
    {
        $gradeScale->delete();
        return back()->with('success', 'Grade deleted.');
    }

    public function toggle(GradeScale $gradeScale)
    {
        $gradeScale->update(['is_active' => ! $gradeScale->is_active]);
        return back()->with('success', $gradeScale->is_active ? 'Grade activated.' : 'Grade deactivated.');
    }

    public function seed()
    {
        foreach (GradeScale::defaultScale() as $row) {
            GradeScale::updateOrCreate(
                ['name' => $row['name']],
                $row + ['is_active' => true]
            );
        }
        return back()->with('success', 'Default grade scale seeded.');
    }
}
