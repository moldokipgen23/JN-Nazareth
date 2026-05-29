<?php

namespace App\Providers;

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
        $teacherLogin = \App\Helpers\login_path('teacher');
        if (str_starts_with($path, 'teacher') || $path === $teacherLogin || str_starts_with($path, $teacherLogin . '/')) {
            app('config')->set('session.cookie', 'teacher_portal_session');
        }

        if (config('app.env') === 'production' || request()->header('x-forwarded-proto') === 'https') {
            URL::forceScheme('https');
        }
    }
}
