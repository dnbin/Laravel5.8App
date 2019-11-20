<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GeonamesCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:geonames:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import cities data from geonames';

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
        \App\Jobs\GeonamesCities::dispatchNow();
    }
}
