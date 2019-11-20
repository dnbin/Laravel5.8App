<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class BookingExchangeRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchange_rates:booking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add exchange rates to Booking feed';

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
        \App\Jobs\BookingExchangeRates::dispatch();
    }
}
