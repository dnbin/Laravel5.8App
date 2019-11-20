<?php

namespace App\Providers;

use App\Models\Entry;
use App\Models\Search;
use App\Observers\EntryObserver;
use App\Observers\SearchObserver;
use Illuminate\Support\Facades\URL;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
	    // URL::forceScheme('https');

	    $this->app->bind('path.public', function () {
		    return base_path() . DIRECTORY_SEPARATOR .'../public_html';
	    });

	    Search::observe(SearchObserver::class);
	    Entry::observe(EntryObserver::class );
    }
}
