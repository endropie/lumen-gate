<?php

namespace App\Providers;

use App\Services\MicroService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('http', function ($app) {
            return new \Illuminate\Http\Client\Factory;
        });

        $this->app->singleton('microservice', function ($app) {
            return new MicroService($app);
        });
    }

    public function boot ()
    {
        $this->app['microservice']->router();
    }
}
