<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Display the teacher login view.
     */
    public function createTeacher(): View
    {
        return view('auth.teacher-login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // Logging in via the teacher login URL — must have a linked teacher profile.
        if (str_starts_with($request->path(), login_path('teacher'))) {
            if ($user && $user->teacher) {
                return redirect()->route('teacher.dashboard');
            }
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            return back()->withErrors([
                'email' => 'No teacher account found. Contact your school administration.',
            ]);
        }

        // Role-aware landing page: pure teachers go straight to their dashboard.
        if ($user && $user->hasRole('teacher')
            && ! $user->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('teacher.dashboard');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $isTeacher = str_starts_with($request->path(), 'teacher');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->to(login_path($isTeacher ? 'teacher' : 'admin'));
    }
}
