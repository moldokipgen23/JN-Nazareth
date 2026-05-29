<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class ClassSubjectController extends Controller
{
    public function index()
    {
        $year = AcademicYear::current();
        $classes = Student::classes();
        $subjects = Subject::orderBy('sort_order')->orderBy('name')->get();

        $classSubjects = $year
            ? ClassSubject::where('academic_year_id', $year->id)->get()->groupBy('class')
            : collect();

        return view('admin.subjects.class-subjects', compact(
            'year', 'classes', 'subjects', 'classSubjects'
        ));
    }

    public function store(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409, 'No active academic year.');

        $data = $request->validate([
            'class'      => 'required|string',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'integer|exists:subjects,id',
            'section'    => 'nullable|string|max:20',
        ]);

        // Remove existing assignments for this class
        ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $data['class'])
            ->when($data['section'] ?? null, fn ($q) => $q->where('section', $data['section']))
            ->delete();

        // Insert new selections
        foreach ($data['subject_ids'] as $subjectId) {
            ClassSubject::create([
                'class' => $data['class'],
                'subject_id' => $subjectId,
                'academic_year_id' => $year->id,
                'section' => $data['section'] ?? null,
            ]);
        }

        return back()->with('success', 'Subjects updated for ' . $data['class']);
    }

    public function updateConfig(Request $request, ClassSubject $classSubject)
    {
        $data = $request->validate([
            'full_marks' => 'nullable|numeric|min:0|max:9999',
            'pass_marks' => 'nullable|numeric|min:0|max:9999',
            'is_optional' => 'boolean',
            'grade_only'  => 'boolean',
        ]);

        $classSubject->update($data);
        return back()->with('success', 'Subject config updated.');
    }
}
