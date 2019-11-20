<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AirlabsCitiesImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'airlabs:cities:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import city codes from Airlabs.co';

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
        \App\Jobs\AirlabsCitiesImport::dispatchNow();
    }
}
