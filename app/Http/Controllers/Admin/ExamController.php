<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassSubject;
use App\Models\Exam;
use App\Models\ExamSubjectMark;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    public function index()
    {
        $exams = Exam::with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $years = AcademicYear::orderByDesc('id')->get();

        return view('admin.exams.index', compact('exams', 'years'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active', true);
        Exam::create($data);

        return back()->with('success', 'Exam created.');
    }

    public function update(Request $request, Exam $exam)
    {
        $data = $this->validated($request, $exam->id);
        $data['is_active'] = $request->boolean('is_active');
        $exam->update($data);

        return back()->with('success', 'Exam updated.');
    }

    public function destroy(Exam $exam)
    {
        $marksCount = $exam->marks()->count();
        if ($marksCount > 0) {
            return back()->with('error', "Cannot delete \"{$exam->name}\": {$marksCount} mark(s) are linked. Remove or approve marks through the Marks dashboard first.");
        }

        $exam->delete();
        return back()->with('success', 'Exam deleted.');
    }

    public function toggle(Exam $exam)
    {
        $exam->update(['is_active' => ! $exam->is_active]);
        return back()->with('success', $exam->is_active ? 'Exam activated.' : 'Exam deactivated.');
    }

    /**
     * Show the per-subject marks config grid for an exam.
     * Rows: every (class, subject) from class_subjects for the exam's year.
     */
    public function marksConfig(Exam $exam)
    {
        $year = $exam->academicYear;
        $rows = ClassSubject::where('academic_year_id', $exam->academic_year_id)
            ->with('subject')->get()
            ->groupBy('class');

        $existing = ExamSubjectMark::where('exam_id', $exam->id)->get()
            ->keyBy(fn ($r) => $r->class.'|'.$r->subject);

        return view('admin.exams.marks-config', compact('exam', 'year', 'rows', 'existing'));
    }

    /**
     * Bulk save marks config for an exam — one row per (class, subject).
     */
    public function saveMarksConfig(Request $request, Exam $exam)
    {
        $data = $request->validate([
            'config'                  => 'required|array',
            'config.*.class'          => 'required|string',
            'config.*.subject'        => 'required|string',
            'config.*.full_marks'     => 'required|numeric|min:1|max:9999',
            'config.*.pass_marks'     => 'required|numeric|min:0|lte:config.*.full_marks',
        ]);

        foreach ($data['config'] as $row) {
            ExamSubjectMark::updateOrCreate(
                ['exam_id' => $exam->id, 'class' => $row['class'], 'subject' => $row['subject']],
                ['full_marks' => $row['full_marks'], 'pass_marks' => $row['pass_marks']]
            );
        }

        return back()->with('success', count($data['config']).' subject marks saved.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'academic_year_id'    => 'required|exists:academic_years,id',
            'name'                => 'required|string|max:120',
            'type'                => ['nullable', Rule::in(\App\Models\Exam::TYPES)],
            'code'                => 'nullable|string|max:40',
            'starts_on'           => 'nullable|date',
            'ends_on'             => 'nullable|date|after_or_equal:starts_on',
            'submission_starts_on' => 'nullable|date',
            'submission_ends_on'   => 'nullable|date|after_or_equal:submission_starts_on',
            'sort_order'          => 'nullable|integer|min:0',
            'notes'               => 'nullable|string|max:1000',
        ]);
    }
}
