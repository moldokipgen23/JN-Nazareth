<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Update teacher phone/photo if user has a linked teacher profile
        if ($user->teacher) {
            $teacherData = [];
            if ($request->filled('phone')) {
                $teacherData['phone'] = $request->input('phone');
            }
            if ($request->filled('qualification')) {
                $teacherData['qualification'] = $request->input('qualification');
            }
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('teachers', 'public');
                $teacherData['photo'] = $path;
            }
            if ($teacherData) {
                $user->teacher->update($teacherData);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
