<?php

namespace App\Providers;

use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        app('config')->set('filesystems.disks.public.root', public_path('storage'));

        $path = request()->path();
        $teacherLogin = login_path('teacher');
        if (str_starts_with($path, 'teacher') || $path === $teacherLogin || str_starts_with($path, $teacherLogin . '/')) {
            app('config')->set('session.cookie', 'teacher_portal_session');
        }

        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }

        // backFresh(): like back() but appends a timestamp query-param so the
        // browser, LiteSpeed, Cloudflare, or any proxy can never serve a cached
        // version of the redirect target. Used after Approve / Reject / Save
        // actions that mutate data, where stale page = wrong UI.
        Redirector::macro('backFresh', function () {
            $url = url()->previous();
            $sep = str_contains($url, '?') ? '&' : '?';
            return $this->to($url . $sep . '_=' . microtime(true));
        });
    }
}
