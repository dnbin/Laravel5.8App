<?php

namespace App\Providers;

use App\Services\ConsoleColor;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;


class ConsoleColorServiceProvider extends ServiceProvider implements DeferrableProvider
{
    public function __construct( $app ) {
        parent::__construct( $app );
    }

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
        //
        $this->app->singleton(ConsoleColor::class, function ($app) {
            $console = new ConsoleColor();
            $console->setLogger(new Log());
            return $console;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ConsoleColor::class];
    }

}
