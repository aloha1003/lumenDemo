<?php

namespace App\Providers;

use App\Services\FileUploaders\FileUploadManager;
use Illuminate\Support\ServiceProvider;

class FileUploadService extends ServiceProvider
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
        $this->app->singleton('clstorage', function ($app) {
            return new FileUploadManager($app);
        });
    }

    public function provides()
    {
        return [
            'clstorage',
        ];
    }
}
