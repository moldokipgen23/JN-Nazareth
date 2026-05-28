<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\NoteAssignment;
use App\Models\SubjectTeacherAssignment;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $year = AcademicYear::current();

        $notes = NoteAssignment::where('submitted_by', $user->id)
            ->when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->orderByDesc('created_at')
            ->get();

        $slots = $year
            ? SubjectTeacherAssignment::where('teacher_id', ($user->teacher?->id ?? $user->id))
                ->where('academic_year_id', $year->id)
                ->get(['class', 'section', 'subject'])
            : collect();

        return view('teacher.notes.index', compact('notes', 'slots', 'year'));
    }

    public function store(Request $request)
    {
        $year = AcademicYear::current();
        abort_unless($year, 409, 'No active academic year.');

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

        $user = auth()->user();
        if (!$user->isAdmin() && !$user->teachesSubject($data['class'], $data['section'], $data['subject'])) {
            abort(403);
        }

        if (!$data['content'] && !$request->hasFile('file')) {
            return back()->withErrors(['content' => 'Provide content or upload a file.'])->withInput();
        }

        $filePath = $fileName = null;
        if ($request->hasFile('file')) {
            $file     = $request->file('file');
            $filePath = $file->store('notes', 'public');
            $fileName = $file->getClientOriginalName();
        }

        NoteAssignment::create([
            'academic_year_id' => $year->id,
            'submitted_by'     => $user->id,
            'class'            => $data['class'],
            'section'          => $data['section'],
            'subject'          => $data['subject'],
            'type'             => $data['type'],
            'title'            => $data['title'],
            'content'          => $data['content'],
            'file_path'        => $filePath,
            'file_name'        => $fileName,
            'due_date'         => $data['due_date'] ?? null,
        ]);

        return back()->with('success', ucfirst($data['type']).' published.');
    }

    public function destroy(NoteAssignment $note)
    {
        abort_unless($note->submitted_by === auth()->id() || auth()->user()->isAdmin(), 403);
        if ($note->file_path) {
            \Storage::disk('public')->delete($note->file_path);
        }
        $note->delete();
        return back()->with('success', 'Deleted.');
    }
}
