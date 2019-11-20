<?php

namespace App\Jobs;

use App\Models\Search;
use App\Services\ConsoleColor;
use App\Services\RapidApiBooking\RapidApiBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SearchBookingGetLocations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Search $search)
    {
        //
        $this->search=$search;
    }

    /**
     * Execute the job.
     * @param ConsoleColor $console
     * @param RapidApiBooking $booking
     *
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     *
     * @return void
     */
    public function handle(ConsoleColor $console,RapidApiBooking $booking):void
    {
        //
        try {
            $console->info(get_class($this).' running..');
            $parameters = [
                'text'         => $this->search->city->name,
                'languagecode' => 'en-us'
            ];
            $this->search->booking_dest_id   = $booking->getDestId( 'city',$parameters );
            $this->search->saveQuietly();
        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }
    }
}
