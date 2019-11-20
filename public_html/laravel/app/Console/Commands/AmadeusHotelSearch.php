<?php

namespace App\Console\Commands;

use App\Jobs\AmadeusHotelSearchJob;
use App\Jobs\SearchAmadeus;
use App\Models\Search;
use Illuminate\Console\Command;

class AmadeusHotelSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'searches:amadeus {search_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grab search data from Amadeus API';

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

            $search=Search::active()->findOrFail((int)$this->argument('search_id'));
            SearchAmadeus::dispatchNow($search);
        }
        else {
            AmadeusHotelSearchJob::dispatchNow();
        }
    }
}
