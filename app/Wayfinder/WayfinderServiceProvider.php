<?php

namespace App\Wayfinder;

use Illuminate\Support\ServiceProvider;

class WayfinderServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            base_path('config/wayfinder.php'),
            'wayfinder',
        );
    }

    public function boot(): void
    {
        $this->loadViewsFrom(resource_path('views/wayfinder'), 'wayfinder');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                resource_path('wayfinder/assets') => public_path(config('wayfinder.assets_path')),
            ], 'wayfinder-assets');
        }
    }
}
