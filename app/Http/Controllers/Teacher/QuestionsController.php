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

        // Teachers only see pending questions (reviewed = hidden to prevent leaks)
        $questions = ExamQuestion::where('submitted_by', $user->id)
            ->where('status', 'pending')
            ->when($year, fn($q) => $q->where('academic_year_id', $year->id))
            ->with(['exam'])
            ->orderByDesc('created_at')
            ->get();

        // Only show exams that are active AND within the submission window
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
                ->where('academic_year_id', $year->id)->get(['class','section','subject'])
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
            'section'       => 'required|string',
            'subject'       => 'required|string',
            'question_text' => 'nullable|string|max:5000',
            'file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'notes'         => 'nullable|string|max:1000',
        ]);

        // Ensure exam is active and within submission window
        $exam = Exam::findOrFail($data['exam_id']);
        $now  = now()->startOfDay();
        abort_unless($exam->is_active, 403, 'Exam is not active.');
        if ($exam->submission_starts_on && $now->lt($exam->submission_starts_on)) {
            abort(403, 'Question submission has not started yet.');
        }
        if ($exam->submission_ends_on && $now->gt($exam->submission_ends_on)) {
            abort(403, 'Question submission deadline has passed.');
        }

        // Authorize slot
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->teachesSubject($data['class'], $data['section'], $data['subject'])) {
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
            'section'          => $data['section'],
            'subject'          => $data['subject'],
            'question_text'    => $data['question_text'] ?? null,
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'notes'            => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Question submitted.');
    }

    public function destroy(ExamQuestion $question)
    {
        abort_unless($question->submitted_by === auth()->id() || auth()->user()->isAdmin(), 403);
        if ($question->file_path) \Storage::disk('public')->delete($question->file_path);
        $question->delete();
        return back()->with('success', 'Question deleted.');
    }
}
