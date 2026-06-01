<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'           => \App\Http\Middleware\RoleMiddleware::class,
        ]);
        $middleware->trustProxies(at: '*');

        $middleware->web(prepend: [
            \App\Http\Middleware\NoHttpCache::class,
            \App\Http\Middleware\TeacherSession::class,
            \App\Http\Middleware\SetWorkingYear::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            // Teacher-protected pages show 404 instead of redirecting to login,
            // EXCEPT for the logout route (needs to work when authenticated).
            if ($request->is('teacher*') && ! $request->is('teacher/logout')) {
                abort(404);
            }
            return redirect()->guest(route('login'));
        });
    })->create();
