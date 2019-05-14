<?php

namespace Jonasva\Vision;

use Illuminate\Support\ServiceProvider;

class VisionServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/vision.php' => config_path('vision.php'),
        ]);
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/vision.php', 'vision');

        $this->app->singleton(Vision::class, function ($app) {
            return new Vision($app->config->get('vision', []));
        });

        $this->app->alias(Vision::class, 'vision');
    }
}
