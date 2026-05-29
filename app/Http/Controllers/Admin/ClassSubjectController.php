<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Mark;
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
            ? ClassSubject::where('academic_year_id', $year->id)->with('subject')->get()->groupBy('class')
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
            'class'         => 'required|string',
            'subject_ids'   => 'nullable|array',
            'subject_ids.*' => 'integer|exists:subjects,id',
            'section'       => 'nullable|string|max:20',
        ]);

        $class = $data['class'];
        $subjectIds = $data['subject_ids'] ?? [];

        // Get existing BEFORE deleting — to check for marks
        $existing = ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($data['section'] ?? null, fn ($q) => $q->where('section', $data['section']))
            ->with('subject')
            ->get();

        // Prevent removing a subject if marks already exist for it
        foreach ($existing as $ex) {
            if (!in_array($ex->subject_id, $subjectIds)) {
                $hasMarks = Mark::where('academic_year_id', $year->id)
                    ->where('class', $class)->where('subject', $ex->subject?->name)->exists();
                if ($hasMarks) {
                    return back()->with('error',
                        "Cannot remove \"{$ex->subject?->name}\" from {$class} — marks already exist for this subject."
                    );
                }
            }
        }

        // Preserve existing is_optional / grade_only flags across re-saves
        $existingFlags = $existing->keyBy('subject_id')
            ->map(fn ($e) => [
                'is_optional' => (bool) $e->is_optional,
                'grade_only'  => (bool) $e->grade_only,
            ])
            ->toArray();

        // Remove existing assignments for this class
        ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $class)
            ->when($data['section'] ?? null, fn ($q) => $q->where('section', $data['section']))
            ->delete();

        // Insert new selections — marks config is per-exam, but flags carry over.
        foreach ($subjectIds as $subjectId) {
            ClassSubject::create([
                'class'             => $class,
                'subject_id'        => $subjectId,
                'academic_year_id'  => $year->id,
                'section'           => $data['section'] ?? null,
                'is_optional'       => $existingFlags[$subjectId]['is_optional'] ?? false,
                'grade_only'        => $existingFlags[$subjectId]['grade_only'] ?? false,
            ]);
        }

        return back()->with('success', 'Subjects updated for ' . $class);
    }

    public function copy(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409);

        $from = $request->query('from');
        $to   = $request->query('to');

        if (!$from || !$to) {
            return back()->with('error', 'Select source and target class.');
        }

        $sourceSubjects = ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $from)->get();

        // Remove existing for target
        ClassSubject::where('academic_year_id', $year->id)
            ->where('class', $to)->delete();

        foreach ($sourceSubjects as $cs) {
            ClassSubject::create([
                'class'             => $to,
                'subject_id'        => $cs->subject_id,
                'academic_year_id'  => $year->id,
                'section'           => $cs->section,
                'full_marks'        => $cs->full_marks,
                'pass_marks'        => $cs->pass_marks,
                'is_optional'       => $cs->is_optional,
                'grade_only'        => $cs->grade_only,
            ]);
        }

        return redirect()->route('admin.class-subjects.index')
            ->with('success', "Subjects copied from {$from} to {$to}.");
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
