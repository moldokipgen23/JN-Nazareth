<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacherAssignment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeacherAssignmentController extends Controller
{
    public function index()
    {
        $activeYear = AcademicYear::current();
        $classes = Student::classes();
        $teachers = Teacher::active()->orderBy('name')->get();
        $subjects = Subject::active()->orderBy('sort_order')->orderBy('name')->get();
        $sections = \App\Models\Section::active()->orderBy('sort_order')->orderBy('name')->get();
        $sectionList = $sections->groupBy('class')->map(fn($g) => $g->pluck('name'))->toJson();

        $classAssignments = ClassTeacherAssignment::with('teacher')
            ->forActiveYear()
            ->orderBy('class')
            ->orderBy('section')
            ->get();

        $subjectAssignments = SubjectTeacherAssignment::with('teacher')
            ->forActiveYear()
            ->orderBy('class')
            ->orderBy('section')
            ->orderBy('subject')
            ->get();

        return view('admin.teacher-assignments.index', compact(
            'activeYear',
            'classes',
            'teachers',
            'subjects',
            'sections',
            'sectionList',
            'classAssignments',
            'subjectAssignments'
        ));
    }

    public function storeClassTeacher(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'class' => ['required', Rule::in(Student::classes())],
            'section' => ['required', 'string', 'max:20', Rule::exists('sections', 'name')->where('class', $request->input('class'))],
        ]);

        $teacher = Teacher::findOrFail($data['teacher_id']);

        $activeYear = AcademicYear::current();
        abort_unless($activeYear, 422, 'Please create an academic year first.');

        $assignment = ClassTeacherAssignment::updateOrCreate(
            [
                'academic_year_id' => $activeYear->id,
                'class' => $data['class'],
                'section' => strtoupper(trim($data['section'])),
            ],
            [
                'teacher_id' => $teacher->id,
            ]
        );

        ActivityLogger::log('class_teacher_assigned', $assignment, "Assigned {$teacher->name} as class teacher.");

        return back()->with('success', 'Class teacher assignment saved.');
    }

    public function destroyClassTeacher(ClassTeacherAssignment $assignment)
    {
        $assignment->delete();

        ActivityLogger::log('class_teacher_unassigned', null, 'Removed a class teacher assignment.');

        return back()->with('success', 'Class teacher assignment removed.');
    }

    public function storeSubjectTeacher(Request $request)
    {
        $data = $request->validate([
            'teacher_id' => ['required', 'integer', 'exists:teachers,id'],
            'class' => ['required', Rule::in(Student::classes())],
            'section' => ['required', 'string', 'max:20', Rule::exists('sections', 'name')->where('class', $request->input('class'))],
            'subjects' => ['required', 'array', 'min:1'],
            'subjects.*' => ['required', 'string', 'max:100'],
        ]);

        $teacher = Teacher::findOrFail($data['teacher_id']);
        $section = strtoupper(trim($data['section']));

        $activeYear = AcademicYear::current();
        abort_unless($activeYear, 422, 'Please create an academic year first.');

        // Remove unchecked subjects for this teacher+class+section
        SubjectTeacherAssignment::where('teacher_id', $teacher->id)
            ->where('academic_year_id', $activeYear->id)
            ->where('class', $data['class'])
            ->where('section', $section)
            ->whereNotIn('subject', $data['subjects'])
            ->delete();

        // Add/update each checked subject
        foreach ($data['subjects'] as $subject) {
            SubjectTeacherAssignment::updateOrCreate(
                [
                    'academic_year_id' => $activeYear->id,
                    'class' => $data['class'],
                    'section' => $section,
                    'subject' => trim($subject),
                ],
                ['teacher_id' => $teacher->id]
            );
        }

        $count = count($data['subjects']);
        ActivityLogger::log('subject_teacher_assigned', null, "Assigned {$teacher->name} to {$count} subject(s).");

        return back()->with('success', "{$count} subject(s) assigned to {$teacher->name}.");
    }

    public function destroySubjectTeacher(SubjectTeacherAssignment $assignment)
    {
        $assignment->delete();

        ActivityLogger::log('subject_teacher_unassigned', null, 'Removed a subject teacher assignment.');

        return back()->with('success', 'Subject teacher assignment removed.');
    }
}
