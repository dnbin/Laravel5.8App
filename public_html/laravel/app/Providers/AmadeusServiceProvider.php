<?php

namespace App\Providers;

use AmadeusDahabtours\SelfServiceApiClient;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class AmadeusServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(SelfServiceApiClient::class, function ($app) {
            return new SelfServiceApiClient(config('amadeus.api_key'),config('amadeus.api_secret'),config('amadeus.env'));
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [SelfServiceApiClient::class];
    }

}
