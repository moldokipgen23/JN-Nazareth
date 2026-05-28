<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gives the teacher portal its own separate session cookie so admin and
 * teacher logins are completely independent on the same device/browser.
 */
class TeacherSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // Switch to a dedicated session cookie for all teacher routes.
        // This means logging in as admin has zero effect on teacher session
        // and vice versa — they are treated as entirely separate portals.
        config(['session.cookie' => 'teacher_portal_session']);

        return $next($request);
    }
}
