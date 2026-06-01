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
            return redirect()->route('login');
        }

        $user = auth($guard)->user();

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                // Set on the default guard for convenience in views
                if ($guard !== 'web') {
                    auth()->setUser($user);
                }
                return $next($request);
            }
        }

        abort(403, 'Unauthorized');
    }
}
