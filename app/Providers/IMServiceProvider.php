<?php

namespace App\Providers;

use App\Services\IM\IMManager;
use Illuminate\Support\ServiceProvider;

class IMServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('im', function ($app) {
            return new IMManager($app);
        });
    }

    public function provides()
    {
        return [
            'im',
        ];
    }
}
