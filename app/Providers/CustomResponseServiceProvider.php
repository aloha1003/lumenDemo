<?php namespace App\Providers;

// use Illuminate\Routing\ResponseFactory;
use Illuminate\Support\ServiceProvider;

class CustomResponseServiceProvider extends ServiceProvider
{
    public function boot()
    {

        $this->app->make('App\Services\ResponseMacroManager');
    }

    public function register()
    {

    }
}
