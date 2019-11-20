<?php

namespace App\Jobs;

use App\Events\FeedImported;
use App\Models\Feed;
use App\Models\Search;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\RapidApiBooking\RapidApiBooking;

class BookingHotelSearch implements ShouldQueue
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
     * @param ConsoleColor $console
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     * @return void
     */
    public function handle(ConsoleColor $console,RapidApiBooking $booking)
    {
        //
        try {
            $console->info( 'Starting Booking Hotel Search' );
            /** @var Feed $feed */
            $feed = Feed::where( 'name', 'booking' )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name.' feed is inactive' );
            }
            Search::expired()->update(['status'=>0]);

            // check ExchangeRates
            BookingExchangeRates::dispatch();

            // grab new entries and store in db
            /** @var Search $search */
            foreach(Search::active()->get() as $search){
                try {
                    SearchBooking::dispatch($search);
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
            $console->info('New Entries has been attached to searches');
            */
            // find booking entries which doesn't have any

            event( new FeedImported( $feed ) );
        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }

    }
}
