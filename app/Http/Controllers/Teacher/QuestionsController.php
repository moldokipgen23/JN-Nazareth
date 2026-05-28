<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $year = AcademicYear::current();
        $now  = now()->startOfDay();

        // Teachers see their pending questions AND questions sent back for revision
        $questions = ExamQuestion::where('submitted_by', $user->id)
            ->whereIn('status', ['pending', 'revision_needed'])
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->with(['exam', 'reviewer'])
            ->orderByDesc('created_at')
            ->get();

        // Exam list (active + within submission window)
        $exams = $year
            ? Exam::forActiveYear()->active()
                ->where(function ($q) use ($now) {
                    $q->whereNull('submission_starts_on')
                      ->orWhere('submission_starts_on', '<=', $now);
                })
                ->where(function ($q) use ($now) {
                    $q->whereNull('submission_ends_on')
                      ->orWhere('submission_ends_on', '>=', $now);
                })
                ->orderBy('sort_order')
                ->get()
            : collect();

        $slots = $year
            ? SubjectTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
                ->unique(fn ($s) => $s->class.'|'.$s->subject)
                ->values()
            : collect();

        return view('teacher.questions.index', compact('questions', 'exams', 'slots', 'year'));
    }

    public function store(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409, 'No active academic year.');

        $data = $request->validate([
            'exam_id'       => 'required|exists:exams,id',
            'class'         => 'required|string',
            'subject'       => 'required|string',
            'marks'         => 'nullable|numeric|min:0|max:999',
            'question_text' => 'nullable|string|max:5000',
            'file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'notes'         => 'nullable|string|max:1000',
        ]);

        $exam = Exam::findOrFail($data['exam_id']);
        $now  = now()->startOfDay();
        abort_unless($exam->is_active, 403, 'Exam is not active.');
        if ($exam->submission_starts_on && $now->lt($exam->submission_starts_on)) {
            abort(403, 'Question submission has not started yet.');
        }
        if ($exam->submission_ends_on && $now->gt($exam->submission_ends_on)) {
            abort(403, 'Question submission deadline has passed.');
        }

        $user = auth()->user();
        if (!$user->isAdmin() && !SubjectTeacherAssignment::where('teacher_id', $user->teacher?->id)
            ->where('academic_year_id', $year->id)
            ->where('class', $data['class'])
            ->where('subject', $data['subject'])
            ->exists()
        ) {
            abort(403);
        }

        if (!$data['question_text'] && !$request->hasFile('file')) {
            return back()->withErrors(['question_text' => 'Provide question text or upload a file.'])->withInput();
        }

        $filePath = $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('questions', 'public');
            $fileName = $file->getClientOriginalName();
        }

        ExamQuestion::create([
            'academic_year_id' => $year->id,
            'exam_id'          => $data['exam_id'],
            'submitted_by'     => $user->id,
            'class'            => $data['class'],
            'subject'          => $data['subject'],
            'marks'            => $data['marks'] ?? null,
            'question_text'    => $data['question_text'] ?? null,
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'notes'            => $data['notes'] ?? null,
            'status'           => 'pending',
        ]);

        $params = $request->input('redirect_params');
        if ($params) {
            return redirect()->route('teacher.questions.index', $params ?: [])->with('success', 'Question added.');
        }
        return back()->with('success', 'Question submitted.');
    }

    // Update a question sent back for revision
    public function update(Request $request, ExamQuestion $question)
    {
        abort_unless($question->submitted_by === auth()->id() || auth()->user()->isAdmin(), 403);
        abort_unless($question->status === 'revision_needed', 403, 'This question is not in revision state.');

        $data = $request->validate([
            'marks'         => 'nullable|numeric|min:0|max:999',
            'question_text' => 'nullable|string|max:5000',
            'file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'notes'         => 'nullable|string|max:1000',
        ]);

        if (!$data['question_text'] && !$request->hasFile('file')) {
            return back()->withErrors(['question_text' => 'Provide question text or upload a file.'])->withInput();
        }

        $attrs = [
            'marks'         => $data['marks'] ?? $question->marks,
            'question_text' => $data['question_text'] ?? $question->question_text,
            'notes'         => $data['notes'] ?? $question->notes,
            'status'        => 'pending',
            'review_notes'  => null,
            'reviewed_by'   => null,
            'reviewed_at'   => null,
        ];

        if ($request->hasFile('file')) {
            if ($question->file_path) \Storage::disk('public')->delete($question->file_path);
            $file = $request->file('file');
            $attrs['file_path'] = $file->store('questions', 'public');
            $attrs['file_name'] = $file->getClientOriginalName();
        }

        $question->update($attrs);

        return back()->with('success', 'Question re-submitted for review.');
    }

    public function destroy(Request $request, ExamQuestion $question)
    {
        abort_unless($question->submitted_by === auth()->id() || auth()->user()->isAdmin(), 403);
        if ($question->file_path) \Storage::disk('public')->delete($question->file_path);
        $question->delete();

        $params = [];
        if ($request->has('exam_id')) $params['exam_id'] = $request->exam_id;
        if ($request->has('class')) $params['class'] = $request->class;
        if ($request->has('subject')) $params['subject'] = $request->subject;
        if ($params) {
            return redirect()->route('teacher.questions.index', $params)->with('success', 'Question deleted.');
        }
        return back()->with('success', 'Question deleted.');
    }
}
