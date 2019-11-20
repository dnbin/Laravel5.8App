<?php

namespace App\Console\Commands;

use App\Events\FeedImported;
use App\Jobs\SearchBooking;
use App\Models\Search;
use Illuminate\Console\Command;

class BookingHotelSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searches:booking {search_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grab search data from Booking using RapidAPI interface';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        if(!empty($this->argument('search_id'))){
            $search=Search::active()->findOrFail($this->argument('search_id'));
            SearchBooking::dispatchNow($search);
        }
        else {
            \App\Jobs\BookingHotelSearch::dispatchNow();
        }


    }
}
