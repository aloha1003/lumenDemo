<?php

namespace App\Providers;

use App\Services\Live\LiveManager;
use Illuminate\Support\ServiceProvider;

class LiveServiceProvider extends ServiceProvider
{
    // use \App\Traits\MagicGetTrait;
    protected $defer = false;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('live', function ($app) {
            return new LiveManager($app);
        });
    }

    public function provides()
    {
        return [
            'live',
        ];
    }
}
