<?php

namespace App\Providers;

use App\Contracts\IdentityVerificationService;
use App\Services\BlueCheckService;
use Illuminate\Support\ServiceProvider;

class IdentityServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IdentityVerificationService::class, BlueCheckService::class);
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
