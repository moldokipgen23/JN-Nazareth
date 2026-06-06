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
            \App\Http\Middleware\TeacherSession::class,   // must be before StartSession
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\NoHttpCache::class,
            \App\Http\Middleware\SetWorkingYear::class,   // needs session started
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->is('teacher*') && ! $request->is('teacher/logout')) {
                abort(404);
            }
            return redirect()->guest(route('login'));
        });
    })->create();
