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
        // Default livewire-tmp is often 0700 and owned by the Docker user, so local
        // artisan serve cannot list/write it. Use a dedicated directory instead.
        config([
            'livewire.temporary_file_upload.directory' => 'livewire-uploads',
        ]);
    }
}
