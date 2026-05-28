<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\Request;

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
            'class' => 'required|string|max:20',
            'name'  => 'required|string|max:20',
        ]);

        if (Section::where('class', $data['class'])->where('name', $data['name'])->exists()) {
            return back()->with('error', "Section \"{$data['name']}\" already exists for {$data['class']}.");
        }

        $data['sort_order'] = Section::where('class', $data['class'])->max('sort_order') + 1;
        Section::create($data);

        return back()->with('success', "Section \"{$data['name']}\" added for {$data['class']}.");
    }

    public function destroy(Section $section)
    {
        $section->delete();
        return back()->with('success', 'Section deleted.');
    }
}
