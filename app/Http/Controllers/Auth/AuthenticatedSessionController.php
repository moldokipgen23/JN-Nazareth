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

        // Logging in via the teacher portal always goes to teacher dashboard.
        if (config('session.cookie') === 'teacher_portal_session') {
            return redirect()->intended(route('teacher.dashboard'));
        }

        // Role-aware landing page: pure teachers go straight to their dashboard.
        $user = $request->user();
        if ($user && $user->hasRole('teacher')
            && ! $user->hasAnyRole(['admin', 'staff'])) {
            return redirect()->intended(route('teacher.dashboard'));
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $isTeacher = config('session.cookie') === 'teacher_portal_session';

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->to(login_path($isTeacher ? 'teacher' : 'admin'));
    }
}
