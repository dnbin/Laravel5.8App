<?php

namespace App\Console\Commands;

use GeoNames\Client;
use Illuminate\Console\Command;

class GeonamesTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'geonames:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geonames Test command';

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
        $g = new Client('trec');
        // get a list of supported endpoints
        $endpoints = $g->getSupportedEndpoints();
        dump($endpoints);
        // get info for country
        // note that I'm using the array destructor introduced in PHP 7.1
        [$country] = $g->countryInfo([
            'country' => 'US',
            'lang'    => 'en', // display info in Russian
        ]);

        dump($country);
/*
        $poi=$g->findNearbyPOIsOSM([
            'lat'=>48.858430,
            'lng'=>2.295798,
            'maxRows'=>50,
            'radius'=>1
        ]);
*/

        $cities=$g->search([
            'q'=>'New York',
            'country_code'=>'US'
        ]);
        dump(array_chunk($cities,10));
        //dump($poi);
    }
}
