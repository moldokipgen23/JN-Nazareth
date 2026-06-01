<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use Illuminate\Support\Facades\Route;

/*
 * Configurable login URLs. The admin/teacher login slugs are editable in
 * Site Customizer → General. The default /login route is intentionally NOT
 * registered, so it returns 404. EMERGENCY_LOGIN_PATH is a hardcoded
 * fallback that always works, in case a bad slug is saved.
 *
 * NOTE: after changing a login slug, the route cache must be cleared
 * (the customizer save does this automatically).
 */
defined('EMERGENCY_LOGIN_PATH') || define('EMERGENCY_LOGIN_PATH', 'cms-recovery-7k3');

Route::middleware('guest')->group(function () {
    // Admin login — named 'login' (used by the auth middleware redirect).
    Route::get(login_path('admin'), [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    Route::post(login_path('admin'), [AuthenticatedSessionController::class, 'store']);

    // Emergency fallback — always available.
    Route::get(EMERGENCY_LOGIN_PATH, [AuthenticatedSessionController::class, 'create']);
    Route::post(EMERGENCY_LOGIN_PATH, [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password/{token}', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Teacher login — separate guard, separate session.
// Uses its own 'guest:teacher' middleware so admin (web guard) can
// simultaneously log in as teacher in a different tab.
Route::middleware('guest:teacher')->group(function () {
    Route::get(login_path('teacher'), [AuthenticatedSessionController::class, 'createTeacher'])
        ->name('teacher.login');
    Route::post(login_path('teacher'), [AuthenticatedSessionController::class, 'store'])
        ->name('teacher.login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
