<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::orderBy('sort_order')->orderBy('name')->get();
        return view('admin.subjects.index', compact('subjects'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:subjects,name',
            'code' => 'nullable|string|max:20',
        ]);

        $data['sort_order'] = Subject::max('sort_order') + 1;
        Subject::create($data);

        return back()->with('success', "Subject \"{$data['name']}\" added.");
    }

    public function update(Request $request, Subject $subject)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:subjects,name,' . $subject->id,
            'code' => 'nullable|string|max:20',
        ]);

        $subject->update($data);
        return back()->with('success', 'Subject updated.');
    }

    public function destroy(Subject $subject)
    {
        $inUse = DB::table('subject_teacher_assignments')
            ->where('subject', $subject->name)->exists();

        if ($inUse) {
            return back()->with('error', "Cannot delete \"{$subject->name}\" — it has active assignments.");
        }

        $subject->delete();
        return back()->with('success', 'Subject deleted.');
    }

    public function toggle(Subject $subject)
    {
        $subject->update(['is_active' => ! $subject->is_active]);
        return back()->with('success', 'Subject ' . ($subject->is_active ? 'deactivated.' : 'activated.'));
    }

    public function reorder(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'integer']);
        foreach ($request->ids as $order => $id) {
            Subject::where('id', $id)->update(['sort_order' => $order]);
        }
        return response()->json(['ok' => true]);
    }
}
