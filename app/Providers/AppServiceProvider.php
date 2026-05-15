<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Store public uploads directly in public/storage/ so no symlink is needed on shared hosting
        app('config')->set('filesystems.disks.public.root', public_path('storage'));
    }
}
