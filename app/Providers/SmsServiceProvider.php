<?php

namespace App\Providers;

use App\Services\Sms\SmsManager;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
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
        $this->app->singleton('sms', function($app){
            return new SmsManager($app);
        });
    }

    public function provides()
    {
        return [
            'sms'
        ];
    }
}
