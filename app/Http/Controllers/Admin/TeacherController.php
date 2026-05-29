<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ClassTeacherAssignment;
use App\Models\SubjectTeacherAssignment;
use App\Models\Teacher;
use App\Models\TeacherYearStatus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = Teacher::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function show(Teacher $teacher)
    {
        $teacher->load('yearStatuses.academicYear');
        $years = AcademicYear::orderByDesc('starts_on')->get();

        return view('admin.teachers.show', compact('teacher', 'years'));
    }

    public function updateYearStatus(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'status'           => ['required', 'in:active,resigned,on_leave,transferred'],
            'effective_from'   => ['nullable', 'date'],
            'effective_to'     => ['nullable', 'date'],
            'remarks'          => ['nullable', 'string', 'max:500'],
        ]);

        TeacherYearStatus::updateOrCreate(
            [
                'teacher_id'       => $teacher->id,
                'academic_year_id' => $data['academic_year_id'],
            ],
            [
                'status'         => $data['status'],
                'effective_from' => $data['effective_from'],
                'effective_to'   => $data['effective_to'],
                'remarks'        => $data['remarks'],
            ]
        );

        ActivityLogger::log('teacher_status_updated', $teacher,
            "Updated {$teacher->name} status to {$data['status']} for year {$data['academic_year_id']}");

        return back()->with('success', 'Yearly status updated.');
    }

    public function create()
    {
        $sections = \App\Models\Section::active()->orderBy('sort_order')->orderBy('name')->get();
        $years = \App\Models\AcademicYear::orderByDesc('id')->get();
        return view('admin.teachers.create', compact('sections', 'years'));
    }

    public function store(Request $request)
    {
        $this->validateCtOverride($request);

        $data = $this->validateTeacher($request);

        $teacher = Teacher::create([
            'name'        => $data['name'],
            'designation' => $data['designation'] ?? null,
            'subjects'    => $data['subjects'] ?? null,
            'phone'       => $data['phone'] ?? null,
            'email'       => $data['email'] ?? null,
            'sort_order'  => $data['sort_order'] ?? 0,
            'is_active'   => $request->boolean('is_active', true),
            'photo'       => $request->hasFile('photo')
                ? $request->file('photo')->store('teachers', 'public') : null,
        ]);

        $this->syncClassTeacher($request, $teacher);
        $this->syncSubjectAssignments($request, $teacher);

        ActivityLogger::log('teacher_created', $teacher, "Added teacher: {$teacher->name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher added to the directory.');
    }

    public function edit(Teacher $teacher)
    {
        $year = AcademicYear::current();
        $sections = \App\Models\Section::active()->orderBy('sort_order')->orderBy('name')->get();
        $years = \App\Models\AcademicYear::orderByDesc('id')->get();
        $ctClasses = $year
            ? $teacher->classTeacherAssignments()
                ->where('academic_year_id', $year->id)
                ->pluck('class')
                ->toArray()
            : [];
        $ctSection = $year
            ? $teacher->classTeacherAssignments()
                ->where('academic_year_id', $year->id)
                ->value('section') ?? 'A'
            : 'A';

        return view('admin.teachers.edit', compact('teacher', 'ctClasses', 'ctSection', 'sections', 'years'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->validateCtOverride($request);

        $data = $this->validateTeacher($request);

        $teacher->name        = $data['name'];
        $teacher->designation = $data['designation'] ?? null;
        $teacher->subjects    = $data['subjects'] ?? null;
        $teacher->phone       = $data['phone'] ?? null;
        $teacher->email       = $data['email'] ?? null;
        $teacher->sort_order  = $data['sort_order'] ?? 0;
        $teacher->is_active   = $request->boolean('is_active', true);

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $teacher->photo = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->save();

        $this->syncClassTeacher($request, $teacher);
        $this->syncSubjectAssignments($request, $teacher);

        ActivityLogger::log('teacher_updated', $teacher, "Updated teacher: {$teacher->name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher updated.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }

        // If the linked user only has teacher role, delete them too
        // so the deleted teacher cannot login anymore.
        $teacher->users()->each(function (User $user) {
            if ($user->isTeacherOnly()) {
                $user->delete();
            } else {
                // User has other roles (admin/staff) — just unlink
                $user->teacher_id = null;
                $user->save();
            }
        });

        $name = $teacher->name;
        $teacher->delete();

        ActivityLogger::log('teacher_deleted', null, "Deleted teacher: {$name}");

        return redirect()->route('admin.teachers.index')
                         ->with('success', 'Teacher removed from the directory.');
    }

    public function createLogin(Teacher $teacher)
    {
        $email = $teacher->email;
        if (! $email) {
            return back()->with('error', 'Set an email for this teacher first before creating a login.');
        }

        // If teacher already has a linked user, regenerate password instead
        if ($teacher->users()->exists()) {
            $user = $teacher->users()->first();
            $password = Str::password(10);
            $user->password = Hash::make($password);
            $user->save();

            ActivityLogger::log('teacher_login_password_reset', $user, "Reset login password for teacher: {$teacher->name}");

            return back()->with([
                'success' => "Password reset for {$teacher->name}. Email: {$email}",
                'generated_password' => $password,
            ]);
        }

        // If an unlinked user with this email exists, re-link them
        $existing = User::where('email', $email)->whereNull('teacher_id')->first();
        if ($existing) {
            $password = Str::password(10);
            $existing->teacher_id = $teacher->id;
            $existing->password = Hash::make($password);
            $existing->save();

            ActivityLogger::log('teacher_login_relinked', $existing, "Re-linked user to teacher: {$teacher->name}");

            return back()->with([
                'success' => "Login re-linked for {$teacher->name}. Email: {$email}",
                'generated_password' => $password,
            ]);
        }

        if (User::where('email', $email)->exists()) {
            return back()->with('error', "A user with email {$email} already exists and is linked to another teacher.");
        }

        $password = Str::password(10);

        $user = User::create([
            'teacher_id' => $teacher->id,
            'name'       => $teacher->name,
            'email'      => $email,
            'password'   => Hash::make($password),
            'phone'      => $teacher->phone,
            'designation'=> $teacher->designation,
        ]);
        $user->assignRole('teacher');

        ActivityLogger::log('teacher_login_created', $user, "Created login for teacher: {$teacher->name}");

        return back()->with([
            'success' => "Login created for {$teacher->name}. Email: {$email}",
            'generated_password' => $password,
        ]);
    }

    public function linkUser(Teacher $teacher, Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $user = User::findOrFail($data['user_id']);

        if ($user->teacher_id) {
            return back()->with('error', 'That user is already linked to another teacher.');
        }

        $user->teacher_id = $teacher->id;
        $user->save();

        ActivityLogger::log('teacher_user_linked', $user, "Linked user {$user->name} to teacher: {$teacher->name}");

        return back()->with('success', "Linked {$user->name} to {$teacher->name}.");
    }

    public function unlinkUser(Teacher $teacher, User $user)
    {
        if ($user->teacher_id !== $teacher->id) {
            return back()->with('error', 'That user is not linked to this teacher.');
        }

        $user->teacher_id = null;
        $user->save();

        ActivityLogger::log('teacher_user_unlinked', null, "Unlinked user {$user->name} from teacher: {$teacher->name}");

        return back()->with('success', "Unlinked {$user->name} from {$teacher->name}.");
    }

    private function syncClassTeacher(Request $request, Teacher $teacher): void
    {
        $year = AcademicYear::current();
        if (! $year) {
            return;
        }

        $selected = $request->input('ct_classes', []);
        $section  = strtoupper(trim($request->input('ct_section', 'A')));
        $valid    = \App\Models\Student::classes();

        // Remove assignments for classes no longer selected
        $teacher->classTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->whereNotIn('class', $selected)
            ->delete();

        // Add/update each selected class
        foreach ($selected as $class) {
            if (! in_array($class, $valid, true)) {
                continue;
            }
            ClassTeacherAssignment::updateOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'class'            => $class,
                    'section'          => $section,
                ],
                ['teacher_id' => $teacher->id]
            );
        }
    }

    private function syncSubjectAssignments(Request $request, Teacher $teacher): void
    {
        $year = AcademicYear::current();
        if (! $year) {
            return;
        }

        $rows = $request->input('subject_assignments', []);
        $validClasses  = \App\Models\Student::classes();
        $validSubjects = \App\Models\Subject::active()->pluck('name')->all();

        // Build the new set as [class, section, subject] keys
        $keep = [];
        foreach ($rows as $row) {
            $class   = trim($row['class'] ?? '');
            $section = strtoupper(trim($row['section'] ?? ''));
            $subject = trim($row['subject'] ?? '');
            if (! $class || ! $section || ! $subject) continue;
            if (! in_array($class, $validClasses, true)) continue;
            if (! in_array($subject, $validSubjects, true)) continue;

            SubjectTeacherAssignment::updateOrCreate(
                [
                    'academic_year_id' => $year->id,
                    'teacher_id'       => $teacher->id,
                    'class'            => $class,
                    'section'          => $section,
                    'subject'          => $subject,
                ],
                []
            );
            $keep[] = "{$class}|{$section}|{$subject}";
        }

        // Delete any existing assignments not in the new set
        $teacher->subjectTeacherAssignments()
            ->where('academic_year_id', $year->id)
            ->get()
            ->each(function ($a) use ($keep) {
                if (! in_array("{$a->class}|{$a->section}|{$a->subject}", $keep, true)) {
                    $a->delete();
                }
            });
    }

    private function validateCtOverride(Request $request): void
    {
        $classes = $request->input('ct_classes', []);
        if (count($classes) > 1 && ! $request->boolean('ct_override')) {
            redirect()->back()
                ->withInput()
                ->withErrors(['ct_override' => 'You must confirm the override when assigning more than one class.'])
                ->throwResponse();
        }
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
            'ct_classes'  => ['nullable', 'array'],
            'ct_classes.*' => ['string', 'max:100'],
            'ct_section'  => ['nullable', 'string', 'max:20'],
            'subject_assignments'             => ['nullable', 'array'],
            'subject_assignments.*.class'     => ['nullable', 'string', 'max:100'],
            'subject_assignments.*.section'   => ['nullable', 'string', 'max:20'],
            'subject_assignments.*.subject'   => ['nullable', 'string', 'max:100'],
        ]);
    }
}
