<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTeacherOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole('teacher')) {
            return response()->json([
                'success' => false,
                'message' => 'Access denied. Teacher role required.',
            ], 403);
        }

        if (! $user->teacher) {
            return response()->json([
                'success' => false,
                'message' => 'No teacher profile linked.',
            ], 403);
        }

        return $next($request);
    }
}
