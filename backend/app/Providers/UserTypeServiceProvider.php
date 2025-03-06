<?php

namespace App\Providers;

use App\Services\UserTypeService;
use Illuminate\Support\ServiceProvider;

class UserTypeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('user.type', function ($app) {
            return new UserTypeService();
        });

        $this->app->singleton('userType', function ($app) {
            return new UserTypeService();
        });

        $this->app->singleton(UserTypeService::class, function ($app) {
            return new UserTypeService();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
