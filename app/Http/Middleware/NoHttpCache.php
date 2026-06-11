<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoHttpCache
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private, max-age=0');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
        // cPanel/LiteSpeed often caches HTML unless explicitly told not to.
        $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
        // Cloudflare / CDN hints — harmless if no proxy in front.
        $response->headers->set('CDN-Cache-Control', 'no-store');
        $response->headers->set('Surrogate-Control', 'no-store');

        return $response;
    }
}
