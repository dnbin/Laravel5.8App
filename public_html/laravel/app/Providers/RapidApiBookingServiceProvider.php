<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use App\Services\RapidApiBooking\RapidApiBooking;

class RapidApiBookingServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->singleton(RapidApiBooking::class, function ($app) {
            return new RapidApiBooking(config('rapidapi.booking.api_key'),config('rapidapi.booking.host'));
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
        return [RapidApiBooking::class];
    }

}
