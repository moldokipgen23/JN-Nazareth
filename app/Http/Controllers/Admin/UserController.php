<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->query('role', 'admin_staff');

        $users = User::with('roles')
            ->when($role === 'teacher', fn ($q) => $q->whereHas('roles', fn ($r) => $r->where('name', 'teacher')))
            ->when($role === 'admin_staff', fn ($q) => $q->whereHas('roles', fn ($r) => $r->whereIn('name', ['admin', 'staff'])))
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users', 'role'));
    }

    public function create()
    {
        $roles   = Role::orderBy('name')->pluck('name');
        $classes = Student::classes();

        return view('admin.users.create', compact('roles', 'classes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'classes'   => ['array'],
            'classes.*' => ['string', 'max:50'],
        ]);

        $user = User::create([
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'password'         => Hash::make($validated['password']),
            'assigned_classes' => $this->classAccess($request, $validated['role']),
        ]);

        $user->assignRole($validated['role']);

        ActivityLogger::log('user_created', $user, "Created user: {$user->name} with role: {$validated['role']}");

        return redirect()->route('admin.users.index')
                         ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles   = Role::orderBy('name')->pluck('name');
        $classes = Student::classes();

        return view('admin.users.edit', compact('user', 'roles', 'classes'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'password'  => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'string', 'exists:roles,name'],
            'classes'   => ['array'],
            'classes.*' => ['string', 'max:50'],
        ]);

        $updateData = [
            'name'             => $validated['name'],
            'email'            => $validated['email'],
            'assigned_classes' => $this->classAccess($request, $validated['role']),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);
        $user->syncRoles([$validated['role']]);

        ActivityLogger::log('user_updated', $user, "Updated user: {$user->name}");

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        ActivityLogger::log('user_deleted', null, "Deleted user: {$name}");

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully.');
    }

    /**
     * Class access list — only kept for the teacher role, and only
     * valid school class names are stored.
     */
    private function classAccess(Request $request, string $role): ?array
    {
        if ($role !== 'teacher') {
            return null;
        }

        return array_values(array_intersect(
            $request->input('classes', []),
            Student::classes()
        ));
    }
}
