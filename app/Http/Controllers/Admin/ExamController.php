<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Exam;
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
        $exam->delete();
        return back()->with('success', 'Exam deleted.');
    }

    public function toggle(Exam $exam)
    {
        $exam->update(['is_active' => ! $exam->is_active]);
        return back()->with('success', $exam->is_active ? 'Exam activated.' : 'Exam deactivated.');
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
