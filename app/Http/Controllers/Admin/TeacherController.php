<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

/**
 * Teacher directory — plain staff records (no login).
 * To give a teacher CMS access, create a User account (teacher role) instead.
 */
class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $classes  = Member::classes();
        $assigned = [];

        return view('admin.teachers.create', compact('classes', 'assigned'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTeacher($request);

        $teacher = Teacher::create([
            'name'        => $data['name'],
            'designation' => $data['designation'] ?? null,
            'subjects'    => $data['subjects'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'email'       => $data['email'] ?? null,
            'classes'     => $this->cleanClasses($request),
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active', true),
            'photo'       => $request->hasFile('photo')
                ? $request->file('photo')->store('teachers', 'public') : null,
        ]);

        ActivityLogger::log('teacher_created', $teacher, "Added teacher: {$teacher->name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher added to the directory.');
    }

    public function edit(Teacher $teacher)
    {
        $classes  = Member::classes();
        $assigned = $teacher->classes ?? [];

        return view('admin.teachers.edit', compact('teacher', 'classes', 'assigned'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $this->validateTeacher($request);

        $teacher->name        = $data['name'];
        $teacher->designation = $data['designation'] ?? null;
        $teacher->subjects    = $data['subjects'] ?? null;
        $teacher->phone       = $data['phone'] ?? null;
        $teacher->email       = $data['email'] ?? null;
        $teacher->classes     = $this->cleanClasses($request);
        $teacher->sort_order  = $data['sort_order'] ?? 0;
        $teacher->is_active   = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save();

        ActivityLogger::log('teacher_updated', $teacher, "Updated teacher: {$teacher->name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        $name = $teacher->name;
        $teacher->delete();

        ActivityLogger::log('teacher_deleted', null, "Deleted teacher: {$name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher removed from the directory.');
    }

    private function validateTeacher(Request $request): array
    {
        return $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:100'],
            'subjects'    => ['nullable', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'email'       => ['nullable', 'email', 'max:255'],
            'photo'       => ['nullable', 'image', 'max:4096'],
            'sort_order'  => ['nullable', 'integer', 'min:0'],
            'classes'     => ['array'],
            'classes.*'   => [Rule::in(Member::classes())],
        ]);
    }

    /** Only keep submitted classes that are valid school classes. */
    private function cleanClasses(Request $request): array
    {
        return array_values(array_intersect(
            $request->input('classes', []),
            Member::classes()
        ));
    }
}
