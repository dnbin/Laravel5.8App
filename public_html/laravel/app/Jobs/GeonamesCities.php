<?php

namespace App\Jobs;

use App\Models\City;
use App\Services\ConsoleColor;
use GeoNames\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Cache\Repository;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Str;

class GeonamesCities implements ShouldQueue
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
    public function handle(ConsoleColor $console,Client $geonames,Repository $cache)
    {
        //
        $console->info('Geonames city data import starts...');
        $cities=City::with('country')->whereNull('lat')->orWhereNull('lng')->get();
        $total_cities=$cities->count();
        $console->info('Found '.$total_cities.' cities without lat/lng');
        foreach($cities as $index=>$city){
            try {
                $console->info( $index . '/' . $total_cities . ' Process ' . $city->name );
                if($cache->has('cache_city_geonames_'.$city->id)){
                    throw new \Exception('City is cached.. will try again later.');
                }
                $results = $geonames->search( [
                    'q'            => $city->name,
                    'country_code' => $city->country->alpha_2
                ] );
                if ( empty( $results ) ) {
                    throw new \Exception( 'Geonames cities is empty' );
                }
                //
                $is_found=false;
                foreach($results as $result){
                    if(Str::startsWith($result->fclName,'city') && Str::startsWith($result->name,$city->name)){
                        // found
                        $is_found=true;
                        $city->lat=$result->lat ?? null;
                        $city->lng=$result->lng ?? null;
                        $city->population=$result->population ?? null;
                        $city->save();
                        //dump($result);
                        $console->info('Lat: '.$city->lat.' Lng: '.$city->lng.' Population: '.$city->population );
                        break;
                    }
                }
                if(!$is_found){
                    dump($results);
                    $cache->put('cache_city_geonames_'.$city->id,now(),now()->addHours(6));
                    $console->warning('City '.$city->name.' is not found');
                }
            }
            catch(\Exception $e){
                $console->error($e->getMessage());
            }
        }
    }
}
