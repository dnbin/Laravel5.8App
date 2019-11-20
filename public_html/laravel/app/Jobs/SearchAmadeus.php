<?php

namespace App\Jobs;

use AmadeusDahabtours\SelfServiceApiClient;
use App\Models\City;
use App\Models\Entry;
use App\Models\Feed;
use App\Models\Search;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class SearchBooking
 * @package App\Jobs
 * Search model get results from Booking API
 */
class SearchAmadeus implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Search $search ) {
        //
        $this->search = $search;
    }

    /**
     * Execute the job.
     *
     * @param ConsoleColor $console
     *
     * @return void
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    public function handle( ConsoleColor $console, SelfServiceApiClient $amadeus ) {
        try {
            $console->info( 'Starting Amadeus API' );
            /** @var Feed $feed */
            $feed = Feed::where( 'name', 'amadeus' )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name . ' feed is inactive' );
            }
            if(!$this->search->feeds()->where('id',$feed->id)->exists()){
                $this->search->feeds()->attach($feed->id,['created_at'=>now()]);
            }
            $feed=$this->search->feeds()->where('id',$feed->id)->withTimestamps()->first();
            if($this->search->frequency==='daily'){
                if(now()->hour>=12){
                    $pivot=$feed->pivot;
                    // check daily after 12 PM
                    if(!empty($pivot->updated_at) && $pivot->updated_at->gte(now()->startOfDay())){
                        throw new \Exception('Search has been checked today. Wait till tomorrow');
                    }
                }
                else{
                    throw new \Exception('Daily searches will be checked after 12:00 PM. Current time is: '.now()->toDateTimeString());
                }
            }


            $console->info( 'Process search id #' . $this->search->id . ' for user: ' . $this->search->user->name );
            $console->warning( 'Search parameters:' );
            $console->warning( 'City: ' . $this->search->city->name );
            if(!empty($this->search->neighborhood_id)) {
                $console->warning( 'Neighborhood: ' . $this->search->neighborhood->name );
            }
            else{
                $console->warning( 'Neighborhood: ' . 'City Center' );
            }
            $console->warning( 'Check-In Date: ' . $this->search->check_in_date );
            $console->warning( 'Nights: ' . $this->search->nights );
            if(!empty($this->search->hotel_class)) {
                $console->warning( 'Hotel Class: ' . $this->search->hotel_class );
            }
            if(!empty($this->search->rating)) {
                $console->warning( 'Rating: ' . $this->search->rating );
            }
            if(!empty($this->search->max_budget)) {
                $console->warning( 'Max Budget: ' . $this->search->max_budget . ' ' . $this->search->max_budget_currency );
            }
            /** @var City $city */
            $city = $this->search->city;

            $parameters=$this->getParameters($this->search);

            dump( $parameters );
            $hotel_search = $amadeus->hotelOffers( $parameters );
            if ( $hotel_search['success'] !== true ) {
                throw new \Exception( 'Something goes wrong.' );
            }
            if ( $hotel_search['http_code'] !== 200 ) {
                throw new \Exception( 'Http error ' . $hotel_search['http_code'] . ' Response: ' . $hotel_search['response_text'] );
            }
            $hotels = $hotel_search['response']['data'];

            // remove all entries with Amadeus feed
            //$feed->entries()->delete();
            $new_entries=[];
            $found_hotels=0;
            if ( empty( $hotels ) ) {
                $entries_to_detach = $this->search->entries()->whereHas( 'feed', function ( $q ) use ( $feed ) {
                    $q->where( 'id', $feed->id );
                } )->get();
                if ( $entries_to_detach->isNotEmpty() ) {
                    //$this->search->entries()->detach( $entries_to_detach->pluck( 'id' ) );
                    foreach ( $entries_to_detach as $entry_to_detach ) {
                        $this->search->entries()->updateExistingPivot( $entry_to_detach->id, [ 'is_latest' => false ] );
                    }
                    //$console->info( 'Old entries has been detached' );
                    $console->info( $entry_to_detach->count() . ' old entries was marked as is_latest=false' );
                }
                $this->search->feeds()->updateExistingPivot($feed->id,['updated_at'=>now()]);
                throw new \Exception( 'No hotels found for search #' . $this->search->id );
            }

            foreach ( $hotels as $hotel ) {
                try {
                    $data = $hotel['hotel'];

                    $price_per_night=null;
                    if ( ! empty( $hotel['offers'][0]['price'] ) ) {
                        if(!empty($hotel['offers'][0]['price']['total'])) {
                            $price_per_night = floor( $hotel['offers'][0]['price']['total'] / $this->search->nights );
                        }
                        elseif(!empty($hotel['offers'][0]['price']['base'])) {
                            $price_per_night = floor( $hotel['offers'][0]['price']['base'] / $this->search->nights );
                        }
                        else{
                            dump($hotel['offers'][0]['price']);
                        }
                    }

                    /** @var Entry $entry */
                    $entry = Entry::firstOrNew( [
                        'feed_id'       => $feed->id,
                        'feed_entry_id' => $data['hotelId']
                    ] );

                    if ( $entry->exists ) {
                        $console->warning( 'Hotel ' . $data['name'] . ' (' . $entry->feed_entry_id . ') will be updated.' );
                    } else {
                        $console->warning( 'Hotel ' . $data['name'] . ' (' . $entry->feed_entry_id . ') will be created.' );
                    }

                    $entry->last_updated_at = Carbon::now();
                    $entry->title           = $data['name'];
                    if ( ! empty( $data['description'] ) ) {
                        $entry->description = $data['description']['text'] ?? null;
                    }
                    $entry->travel_type = $data['type'];
                    if ( ! empty( $data['address']['lines'] ) ) {
                        $entry->street_address = $data['address']['lines'][0];
                    }

                    //$entry->city=$data['cityCode'] ?? null;
                    $entry->city           = $this->search->city->name;
                    $entry->province_state = $data['address']['stateCode'] ?? null;
                    $entry->zip_code       = $data['address']['postalCode'] ?? null;
                    $entry->country        = $data['address']['countryCode'];
                    if ( ! empty( $data['contact']['phone'] ) ) {
                        $entry->phone_number = $data['contact']['phone'];
                    }
                    $entry->longitude = $data['longitude'] ?? null;
                    $entry->latitude  = $data['latitude'] ?? null;
                    $entry->price          = $price_per_night;
                    if ( ! empty( $hotel['offers'][0]['price'] ) ) {
                        $entry->currency = $hotel['offers'][0]['price']['currency'] ?? null;
                    }
                    $entry->link = $hotel['self'];
                    if ( ! empty( $data['media'] ) ) {
                        $entry->image_link = $data['media'][0]['uri'] ?? null;
                    }
                    $entry->star_rating = $data['rating'] ?? null;
                    //$entry->custom_label_0='';
                    //$entry->custom_label_1='';
                    //$entry->baths='';
                    //$entry->bedrooms='';
                    $entry->save();
                    $console->info( 'Saved. Price per Night: ' . $entry->price . $entry->currency );
                    // check entry against search requirements
                    if ( $entry->searchValidate( $this->search, $feed ) ) {
                        // attach it if fit
                        $console->info( 'Hotel offer was validated' );
                        // check price rules against pivot saved snapshot
                        $entry_with_pivot = $this->search->entries()->where( 'id', $entry->id )->first();
                        // validate price against sent snapshot
                        if ( ! empty( $entry_with_pivot ) && ! empty( $entry_with_pivot->pivot->sent_at ) && ! empty( $entry_with_pivot->pivot->sent_snapshot ) ) {

                            //dump($entry_with_pivot->pivot->sent_snapshot);
                            $sent_snapshot_price = (float) $entry_with_pivot->pivot->sent_snapshot['price'];
                            $console->warning( 'Sent snapshot price: ' . $sent_snapshot_price );
                            try {
                                $entry->priceValidate( $sent_snapshot_price );
                                // mark as new, save new snapshot
                                $this->search->entries()->syncWithoutDetaching( [
                                    $entry->id => [
                                        'is_latest'     => true,
                                        'sent_at'       => null,
                                        'sent_snapshot' => null,
                                        'updated_at'    => now()
                                    ]
                                ] );
                            } catch ( \Exception $e ) {
                                $this->search->entries()->syncWithoutDetaching( [
                                    $entry->id => [
                                        'is_latest'  => true,
                                        'updated_at' => now()
                                    ]
                                ] );
                                $console->error( $e->getMessage() );
                            }
                        } else {
                            // new offer. should sent anyway.
                            // mark as new, save new snapshot
                            $console->warning( 'New offer. Should sent anyway' );
                            $this->search->entries()->syncWithoutDetaching( [
                                $entry->id => [
                                    'is_latest'     => true,
                                    'sent_at'       => null,
                                    'sent_snapshot' => null,
                                    'updated_at'    => now()
                                ]
                            ] );
                        }

                        if ( ! in_array( $data['hotelId'], $new_entries ) ) {
                            $new_entries[] = $data['hotelId'];
                        }

                    } else {
                        $console->warning( 'Hotel was not validated' );
                    }
                } catch ( \Exception $e ) {
                    $console->error( $e->getMessage() );
                }

            }
            $console->info('Found '.count($new_entries).' matched hotels.');
            // remove old entries with current feed
            $entries_to_detach = $this->search
                ->entries()
                ->whereHas( 'feed', function ( $q ) use ( $feed ) {
                    $q->where( 'id', $feed->id );
                } )
                ->whereNotIn( 'feed_entry_id', $new_entries )
                ->get();
            if ( $entries_to_detach->isNotEmpty() ) {
                //$this->search->entries()->detach( $entries_to_detach->pluck( 'id' ) );
                foreach ( $entries_to_detach as $entry_to_detach ) {
                    $this->search->entries()->updateExistingPivot( $entry_to_detach->id, [ 'is_latest' => false ] );
                }
            }
            //$console->info('Old entries has been detached');
            $console->info( $entries_to_detach->count().' old entries was marked as is_latest=false' );

        }
        catch(\Exception $e){
            $console->error($e->getMessage());
        }
    }

    protected function getParameters(Search $search,string $search_id=null,int $offset=0):array {
        $parameters = [
            'cityCode'   => $search->city->iata_code,
            'checkInDate'=>$search->check_in_date->toDateString(),
            'checkOutDate'=>$search->check_in_date->addDays($search->nights)->toDateString(),
            'currency'   => 'USD', // default.. override it if max_budget speciifed
            'sort'       => 'PRICE', // DISTANCE
            'view'       => 'FULL',
            'adults'     => $search->number_of_adults
        ];

        if(!empty($search->max_budget)) {
            //$price_per_night = floor( $this->search->max_budget / $this->search->nights );
            $price_per_night = $search->max_budget;

            //
            //            /*
            //             *    'parameters' => [
            //            'cityCode',         'latitude',        'longitude',        'hotelIds',        'checkInDate',        'checkOutDate',        'roomQuantity',
            //            'adults',        'childAges',        'radius',        'radiusUnit',        'hotelName',        'chains',        'rateCodes',        'amenities',
            //            'ratings',        'priceRange',        'currency',        'paymentPolicy',        'boardType',        'includeClosed',        'bestRateOnly',
            //            'view',        'sort',        'page[limit]',        'page[offset]',        'lang'      ],
            //             */
            $parameters['priceRange'] = '1-' . $price_per_night;
            $parameters['currency']=$search->max_budget_currency;
        }
        if(!empty($search->children)){
            //$parameters['childAges']=implode(',',$this->search->children);
        }

        // filter by hotel class only if class >1, otherwise return all hotels
        if (!empty($search->hotel_class) && $search->hotel_class > 1 ) {
            $parameters['ratings'] = implode( ',', range( $search->hotel_class, 5 ) );
        }
        return $parameters;
    }
}
