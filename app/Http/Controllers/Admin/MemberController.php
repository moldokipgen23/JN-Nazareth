<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = Member::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $members = $query->orderBy('name')->paginate(15)->withQueryString();

        $counts = [
            'all'       => Member::count(),
            'active'    => Member::where('status', 'active')->count(),
            'deceased'  => Member::where('status', 'deceased')->count(),
            'moved_out' => Member::where('status', 'moved_out')->count(),
            'inactive'  => Member::where('status', 'inactive')->count(),
        ];

        return view('admin.members.index', compact('members', 'counts'));
    }

    public function create()
    {
        return view('admin.members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255', 'unique:members,email'],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'status'        => ['nullable', 'in:active,deceased,moved_out,inactive'],
            'role'          => ['nullable', 'string', 'max:100'],
            'notes'         => ['nullable', 'string'],
        ]);

        if (!isset($validated['status'])) {
            $validated['status'] = 'active';
        }

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member = Member::create($validated);

        ActivityLogger::log('member_created', $member, "Created member: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'Member created successfully.');
    }

    public function show(Member $member)
    {
        $member->load('files');

        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['nullable', 'email', 'max:255', 'unique:members,email,' . $member->id],
            'phone'         => ['nullable', 'string', 'max:50'],
            'address'       => ['nullable', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'status'        => ['nullable', 'in:active,deceased,moved_out,inactive'],
            'role'          => ['nullable', 'string', 'max:100'],
            'notes'         => ['nullable', 'string'],
        ]);

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk('public')->delete($member->photo);
            }
            $validated['photo'] = $request->file('photo')->store('members/photos', 'public');
        }

        $member->update($validated);

        ActivityLogger::log('member_updated', $member, "Updated member: {$member->name}");

        return redirect()->route('admin.members.show', $member)
                         ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $files = $member->files;
        foreach ($files as $file) {
            Storage::disk('local')->delete($file->path);
        }

        Storage::disk('local')->deleteDirectory("members/{$member->id}");

        if ($member->photo) {
            Storage::disk('public')->delete($member->photo);
        }

        $name = $member->name;
        $member->delete();

        ActivityLogger::log('member_deleted', null, "Deleted member: {$name}");

        return redirect()->route('admin.members.index')
                         ->with('success', 'Member deleted successfully.');
    }
}
