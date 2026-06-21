<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (! $user || ! \Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->hasRole('teacher')) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Only teachers can use this app.',
            ], 403);
        }

        if (! $user->teacher) {
            $teacher = Teacher::where('email', $user->email)->first();
            if ($teacher) {
                $user->teacher_id = $teacher->id;
                $user->save();
            }
        }

        if (! $user->teacher) {
            return response()->json([
                'success' => false,
                'message' => 'No teacher profile linked. Ask admin to use "Create Login" on your teacher profile.',
            ], 403);
        }

        $token = $user->createToken('teacher-app')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role,
                'teacher_id' => $user->teacher_id,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out.',
        ]);
    }
}
