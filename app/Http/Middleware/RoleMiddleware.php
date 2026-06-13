<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $guard = $request->is('teacher*') ? 'teacher' : 'web';

        if (! auth($guard)->check()) {
            if ($request->is('teacher*')) {
                abort(404);
            }
            // Hard separation: if a teacher is logged in (on the teacher guard)
            // and accidentally hits an /admin/* URL, do not bounce them to the
            // admin login form — that makes it feel like a portal swap. 404 it,
            // so the teacher portal stays the teacher portal.
            if (auth('teacher')->check()) {
                abort(404);
            }
            return redirect()->route('login');
        }

        $user = auth($guard)->user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                // Make the teacher user available on the default web guard
                // for views that call Auth::user() / auth()->user().
                if ($guard !== 'web') {
                    auth()->setUser($user);
                }
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
