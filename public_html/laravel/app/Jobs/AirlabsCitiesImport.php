<?php

namespace App\Jobs;

use App\Models\City;
use App\Models\Country;
use App\Services\ConsoleColor;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Airlabs;

class AirlabsCitiesImport implements ShouldQueue
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
     *
     * @return void
     */
    public function handle(Airlabs $airlabs,ConsoleColor $console)
    {
        $content=$airlabs->cities();
        foreach($content->response as $row){
            try {
                /** @var City $city */
                $city       = City::firstOrNew( [
                    'iata_code' => $row->code
                ] );
                $city->name = $row->name;
                /** @var Country $country */
                $country    = Country::where( 'alpha_2', $row->country_code )->firstOrFail();
                $city->country_id=$country->id;
                $city->save();
                $console->info('City '.$city->name.' has been saved. IATA Code: '.$city->iata_code.' Country: '.$country->name);
            }
            catch(\Exception $e){
                $console->error($e->getMessage());
            }
        }
    }
}
