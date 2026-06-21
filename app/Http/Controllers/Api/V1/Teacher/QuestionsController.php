<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = AcademicYear::current();
        $now  = now()->startOfDay();

        $questions = ExamQuestion::where('submitted_by', $user->id)
            ->whereIn('status', ['pending', 'revision_needed'])
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->with(['exam', 'reviewer'])
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($q) => [
                'id'            => $q->id,
                'exam_name'     => $q->exam?->name,
                'class'         => $q->class,
                'subject'       => $q->subject,
                'marks'         => $q->marks,
                'question_text' => $q->question_text,
                'file_path'     => $q->file_path ? asset('storage/' . $q->file_path) : null,
                'file_name'     => $q->file_name,
                'notes'         => $q->notes,
                'status'        => $q->status,
                'review_notes'  => $q->review_notes,
                'created_at'    => $q->created_at->toIso8601String(),
            ]);

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
                ->map(fn ($e) => ['id' => $e->id, 'name' => $e->name])
            : collect();

        $slots = $year
            ? SubjectTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
                ->unique(fn ($s) => $s->class . '|' . $s->subject)
                ->values()
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'questions' => $questions,
                'exams'     => $exams,
                'slots'     => $slots,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $year = AcademicYear::current();
        if (! $year) {
            return response()->json(['success' => false, 'message' => 'No active academic year.'], 409);
        }

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

        if (! $exam->is_active) {
            return response()->json(['success' => false, 'message' => 'Exam is not active.'], 403);
        }
        if ($exam->submission_starts_on && $now->lt($exam->submission_starts_on)) {
            return response()->json(['success' => false, 'message' => 'Submission not started yet.'], 403);
        }
        if ($exam->submission_ends_on && $now->gt($exam->submission_ends_on)) {
            return response()->json(['success' => false, 'message' => 'Submission deadline passed.'], 403);
        }

        $user = $request->user();
        if (! $user->isAdmin() && ! SubjectTeacherAssignment::where('teacher_id', $user->teacher?->id)
            ->where('academic_year_id', $year->id)
            ->where('class', $data['class'])
            ->where('subject', $data['subject'])
            ->exists()
        ) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if (! ($data['question_text'] ?? null) && ! $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Provide question text or upload a file.'], 422);
        }

        $filePath = $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('questions', 'public');
            $fileName = $file->getClientOriginalName();
        }

        $question = ExamQuestion::create([
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

        return response()->json([
            'success' => true,
            'message' => 'Question submitted.',
            'data'    => ['id' => $question->id],
        ]);
    }

    public function update(Request $request, ExamQuestion $question): JsonResponse
    {
        $user = $request->user();
        if ($question->submitted_by !== $user->id && ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }
        if ($question->status !== 'revision_needed') {
            return response()->json(['success' => false, 'message' => 'Question not in revision state.'], 403);
        }

        $data = $request->validate([
            'marks'         => 'nullable|numeric|min:0|max:999',
            'question_text' => 'nullable|string|max:5000',
            'file'          => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:20480',
            'notes'         => 'nullable|string|max:1000',
        ]);

        if (! ($data['question_text'] ?? null) && ! $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Provide question text or upload a file.'], 422);
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

        return response()->json([
            'success' => true,
            'message' => 'Question re-submitted for review.',
        ]);
    }

    public function destroy(Request $request, ExamQuestion $question): JsonResponse
    {
        $user = $request->user();
        if ($question->submitted_by !== $user->id && ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if ($question->file_path) \Storage::disk('public')->delete($question->file_path);
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted.',
        ]);
    }
}
