<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $teacher = $user->teacher;

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id'    => $user->id,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'photo' => $user->photo ? asset('storage/' . $user->photo) : null,
                ],
                'teacher' => $teacher ? [
                    'id'          => $teacher->id,
                    'name'        => $teacher->name,
                    'designation' => $teacher->designation,
                    'phone'       => $teacher->phone,
                    'email'       => $teacher->email,
                    'photo'       => $teacher->photo ? asset('storage/' . $teacher->photo) : null,
                ] : null,
            ],
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|max:255',
            'phone'    => 'nullable|string|max:20',
            'photo'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) \Storage::disk('public')->delete($user->photo);
            $data['photo'] = $request->file('photo')->store('users', 'public');
        }

        $user->update(collect($data)->filter()->toArray());

        return response()->json([
            'success' => true,
            'message' => 'Profile updated.',
        ]);
    }
}
