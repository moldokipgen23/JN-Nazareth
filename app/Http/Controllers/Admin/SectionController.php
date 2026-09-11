<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentEnrollment;
use App\Models\ClassTeacherAssignment;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SectionController extends Controller
{
    public function index()
    {
        $classes = Student::classes();
        $sections = Section::active()->orderBy('class')->orderBy('sort_order')->orderBy('name')->get()->groupBy('class');

        return view('admin.sections.index', compact('classes', 'sections'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'class' => ['required', Rule::in(Student::classes())],
            'name'  => 'required|string|max:20',
        ]);

        $data['name'] = trim($data['name']);
        if (strcasecmp($data['class'], $data['name']) === 0) {
            return back()->withInput()->with('error', 'A section name cannot be the same as its class name.');
        }

        if (Section::where('class', $data['class'])->where('name', $data['name'])->exists()) {
            return back()->with('error', "Section \"{$data['name']}\" already exists for {$data['class']}.");
        }

        $data['sort_order'] = Section::where('class', $data['class'])->max('sort_order') + 1;
        Section::create($data);

        return back()->with('success', "Section \"{$data['name']}\" added for {$data['class']}.");
    }

    public function destroy(Section $section)
    {
        $inUse = StudentEnrollment::where('class', $section->class)->where('section', $section->name)->exists()
            || ClassTeacherAssignment::where('class', $section->class)->where('section', $section->name)->exists()
            || SubjectTeacherAssignment::where('class', $section->class)->where('section', $section->name)->exists();

        if ($inUse) {
            return back()->with('error', 'This section is in use and cannot be deleted. Move its records first.');
        }

        $section->delete();
        return back()->with('success', 'Section deleted.');
    }
}
