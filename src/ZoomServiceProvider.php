<?php

namespace Pkc\ZoomMeeting;

use Illuminate\Support\ServiceProvider;

class ZoomServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/zoom.php', 'zoom');

        $this->app->singleton(ZoomService::class, function ($app) {
            return new ZoomService();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/zoom.php' => config_path('zoom.php'),
            ], 'zoom-config');
        }
    }
}
