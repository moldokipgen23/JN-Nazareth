<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeacherSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->isTeacherRoute($request)) {
            config(['session.cookie' => 'teacher_portal_session']);
        }

        return $next($request);
    }

    private function isTeacherRoute(Request $request): bool
    {
        $path = $request->path();

        if (str_starts_with($path, 'teacher')) {
            return true;
        }

        $teacherLogin = login_path('teacher');
        if ($path === $teacherLogin || str_starts_with($path, $teacherLogin . '/')) {
            return true;
        }

        return false;
    }
}
