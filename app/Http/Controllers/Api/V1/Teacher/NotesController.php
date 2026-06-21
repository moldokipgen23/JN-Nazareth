<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\NoteAssignment;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $year = AcademicYear::current();

        $notes = NoteAssignment::where('submitted_by', $user->id)
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'type'       => $n->type,
                'title'      => $n->title,
                'content'    => $n->content,
                'class'      => $n->class,
                'section'    => $n->section,
                'subject'    => $n->subject,
                'file_path'  => $n->file_path ? asset('storage/' . $n->file_path) : null,
                'file_name'  => $n->file_name,
                'due_date'   => $n->due_date?->toDateString(),
                'created_at' => $n->created_at->toIso8601String(),
            ]);

        $slots = $year
            ? SubjectTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
            : collect();

        return response()->json([
            'success' => true,
            'data' => [
                'notes' => $notes,
                'slots' => $slots,
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
            'class'     => 'required|string',
            'section'   => 'required|string',
            'subject'   => 'required|string',
            'type'      => 'required|in:note,assignment',
            'title'     => 'required|string|max:255',
            'content'   => 'nullable|string|max:10000',
            'file'      => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:20480',
            'due_date'  => 'nullable|date|after_or_equal:today',
        ]);

        $user = $request->user();
        if (! $user->isAdmin() && ! $user->teachesSubject($data['class'], $data['section'], $data['subject'])) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if (! ($data['content'] ?? null) && ! $request->hasFile('file')) {
            return response()->json(['success' => false, 'message' => 'Provide content or upload a file.'], 422);
        }

        $filePath = $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('notes', 'public');
            $fileName = $file->getClientOriginalName();
        }

        $note = NoteAssignment::create([
            'academic_year_id' => $year->id,
            'submitted_by'     => $user->id,
            'class'            => $data['class'],
            'section'          => $data['section'],
            'subject'          => $data['subject'],
            'type'             => $data['type'],
            'title'            => $data['title'],
            'content'          => $data['content'] ?? null,
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'due_date'         => $data['due_date'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => ucfirst($data['type']) . ' published.',
            'data'    => ['id' => $note->id],
        ]);
    }

    public function destroy(Request $request, NoteAssignment $note): JsonResponse
    {
        $user = $request->user();
        if ($note->submitted_by !== $user->id && ! $user->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Not authorized.'], 403);
        }

        if ($note->file_path) \Storage::disk('public')->delete($note->file_path);
        $note->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted.',
        ]);
    }
}
