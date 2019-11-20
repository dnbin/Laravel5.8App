<?php

namespace App\Jobs;

use AmadeusDahabtours\SelfServiceApiClient;
use App\Events\FeedImported;
use App\Models\City;
use App\Models\Entry;
use App\Models\Feed;
use App\Models\Search;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class AmadeusHotelSearchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @param SelfServiceApiClient $amadeus
     * @param ConsoleColor $console
     *
     * @return void
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function handle(SelfServiceApiClient $amadeus,ConsoleColor $console)
    {
        //
        try {
            $console->info( 'Starting Amadeus Hotel Search' );
            /** @var Feed $feed */
            $feed = Feed::where( 'name', 'amadeus' )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name.' feed is inactive' );
            }
            // check expired searches
            Search::expired()->update(['status'=>0]);

            // grab new entries and store in db
            /** @var Search $search */
            foreach(Search::active()->get() as $search){
                try {
                    SearchAmadeus::dispatch($search);
                }
                catch(\Exception $e){
                    $console->error($e->getMessage());
                }
            }
            // attach new entries to searches.
            /*
            foreach(Search::all() as $search) {
                SaveEntriesForSearch::dispatch( $search );
            }
            */
            $console->info('New Entries has been attached to searches');

            //event( new FeedImported( $feed ) );
        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }
    }
}
