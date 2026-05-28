<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\NoteAssignment;
use App\Models\StudentEnrollment;
use Illuminate\Http\Request;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::current();

        $class   = $request->query('class');
        $section = $request->query('section');
        $type    = $request->query('type');

        $slots = $year
            ? StudentEnrollment::forActiveYear()->active()
                ->select('class', 'section')
                ->groupBy('class', 'section')
                ->get()
            : collect();

        $order = array_flip(Student::classes());
        $slots = $slots->sortBy(fn ($s) => [$order[$s->class] ?? 999, $s->section])->values();

        $notes = NoteAssignment::when($year, fn ($q) => $q->where('academic_year_id', $year->id))
            ->when($class, fn ($q) => $q->where('class', $class))
            ->when($section, fn ($q) => $q->where('section', $section))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->with('submitter')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.notes.index', compact('year', 'slots', 'class', 'section', 'type', 'notes'));
    }

    public function destroy(NoteAssignment $note)
    {
        if ($note->file_path) {
            \Storage::disk('public')->delete($note->file_path);
        }
        $note->delete();
        return back()->with('success', 'Note deleted.');
    }
}
