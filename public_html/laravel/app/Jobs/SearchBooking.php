<?php

namespace App\Jobs;

use App\Models\Entry;
use App\Models\Feed;
use App\Models\Search;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\RapidApiBooking\RapidApiBooking;

/**
 * Class SearchBooking
 * @package App\Jobs
 * Search model get results from Booking API
 */
class SearchBooking implements ShouldQueue {
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $search;
    protected $console;
    protected $feed;
    const MAX_PAGES=5;

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
    public function handle( ConsoleColor $console, RapidApiBooking $booking ) {
        //
        // grab new entries and store in db
        try {
            $console->info( 'Starting Booking Hotel API' );
            /** @var Feed $feed */
            $feed = Feed::where( 'name', 'booking' )->firstOrFail();
            if ( ! $feed->status ) {
                throw new \Exception( $feed->name . ' feed is inactive' );
            }
            if(!$this->search->feeds()->where('id',$feed->id)->exists()){
                $this->search->feeds()->attach($feed->id,['created_at'=>now()]);
            }
            $feed=$this->search->feeds()->where('id',$feed->id)->withTimestamps()->first();

            // load pivot data
            // check pivot updated_at. If frequency daily then compare current time with daily and proceed
            //$this->search->feeds()->updateExistingPivot($feed->id,['updated_at'=>now()]);
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

            $console->warning( 'Check-in Date: ' . $this->search->check_in_date );
            $console->warning( 'Nights: ' . $this->search->nights );
            if ( ! empty( $this->search->hotel_class ) ) {
                $console->warning( 'Hotel Class: ' . $this->search->hotel_class );
            }
            if ( ! empty( $this->search->rating ) ) {
                $console->warning( 'Rating: ' . $this->search->rating );
            }
            if ( ! empty( $this->search->max_budget ) ) {
                $console->warning( 'Max Budget: ' . $this->search->max_budget . ' ' . $this->search->max_budget_currency );
            }

            if ( empty( $this->search->booking_dest_id ) ) {
                $console->info( 'Booking Dest Id is empty. Search it..' );
                $parameters = [
                    'text'         => $this->search->city->name,
                    'languagecode' => 'en-us'
                ];
                try {
                    $dest_id = $booking->getDestId( 'city', $parameters );
                    if ( ! empty( $dest_id ) ) {
                        $this->search->updated_at      = Carbon::now();
                        $this->search->booking_dest_id = $dest_id;
                        $this->search->saveQuietly(); // save without observer events
                        $console->warning( 'Booking DestId: ' . $this->search->booking_dest_id . ' found' );
                    } else {
                        throw new \Exception( 'Dest_id is not found' );
                    }
                } catch ( \Exception $e ) {
                    $console->error( $e->getMessage() );
                }

                if ( empty( $this->search->booking_dest_id ) ) {
                    throw new \Exception( 'booking_dest_id is empty' );
                }
            }
            //$price_per_night = floor( $this->search->max_budget / $this->search->nights );

            // check frequency period for the search
            //if(!$this->search->feeds())


            $page=0;
            $search_id=null;
            $offset=0;
            $found_hotels=0;
            while($page<self::MAX_PAGES) {
                $page++;
                $console->info('Page: '.$page);
                $parameters = $this->getParameters( $booking, $this->search,$search_id,$offset );
                dump( $parameters );
                $response    = $booking->getPropertiesList( $parameters );
                $new_entries = [];
                if ( $response->count === 0 ) {
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
                    //throw new \Exception( 'Hotels is not found' );
                    break;
                }
                else{
                    $found_hotels+=$response->count;
                }

                //dump($response);
                foreach ( $response->result as $hotel ) {
                    try {
                        $price_per_night = null;
                        if ( ! empty( $hotel->min_total_price ) ) {
                            $price_per_night = floor( $hotel->min_total_price / $this->search->nights );
                        }

                        //dd($hotel);
                        /** @var Entry $entry */
                        $entry = Entry::firstOrNew( [
                            'feed_id'       => $feed->id,
                            'feed_entry_id' => $hotel->hotel_id
                        ] );
                        if ( $entry->exists ) {
                            $console->warning( 'Hotel ' . $hotel->hotel_name . ' (' . $entry->feed_entry_id . ') will be updated.' );
                            // check condition
                            //$entry->priceValidate($price_per_night);
                        } else {
                            $console->warning( 'Hotel ' . $hotel->hotel_name . ' (' . $entry->feed_entry_id . ') will be created.' );
                        }
                        $entry->last_updated_at = Carbon::now();
                        $entry->title           = $hotel->hotel_name;
                        /*
                        if ( ! empty( $data['description'] ) ) {
                            $entry->description = $data['description']['text'] ?? null;
                        }
                        */
                        $entry->travel_type    = $hotel->accommodation_type_name ?? null;
                        $entry->street_address = $hotel->address ?? null;
                        $entry->city           = $this->search->city->name;
                        $entry->province_state = $hotel->district ?? null;
                        $entry->zip_code       = $hotel->zip ?? null;
                        $entry->country        = $hotel->countrycode ?? null;
                        $entry->phone_number   = null;
                        $entry->longitude      = $hotel->longitude ?? null;
                        $entry->latitude       = $hotel->latitude ?? null;
                        $entry->price          = $price_per_night;
                        $entry->currency       = $hotel->currency_code ?? null;
                        $entry->link           = $hotel->url ?? null;
                        $entry->image_link     = $hotel->main_photo_url ?? null;
                        $entry->star_rating    = $hotel->class ?? null;
                        $entry->review_score   = $hotel->review_score ?? null;
                        $entry->location_score = $hotel->location_score ?? null;
                        $entry->updated_at     = Carbon::now();
                        //dump($hotel);
                        if ( ! empty( $hotel->rack_rate_savings ) ) {
                            if ( ! empty( $hotel->rack_rate_savings->saving_full_price ) ) {
                                $entry->saving_full_price = (float) $hotel->rack_rate_savings->saving_full_price;
                            }
                            if ( ! empty( $hotel->rack_rate_savings->saving_percentage ) ) {
                                $entry->saving_percentage = (int) $hotel->rack_rate_savings->saving_percentage;
                            }
                        }

                        $entry->save();
                        $console->info( 'Saved. Min Total Price: ' . $hotel->min_total_price . ' Price per Night: ' . $entry->price . $entry->currency );

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

                            if ( ! in_array( $hotel->hotel_id, $new_entries ) ) {
                                $new_entries[] = $hotel->hotel_id;
                            }

                        } else {
                            $console->warning( 'Hotel was not validated' );
                        }
                    } catch ( \Exception $e ) {
                        $console->error( $e->getMessage() );
                    }
                }
                $console->info( 'Found ' . count( $new_entries ) . ' matched hotels.' );
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

                //$console->info( 'Old entries has been detached' );
                //$console->info( $entries_to_detach->count().' old entries was marked as is_latest=false' );

                $search_id=$response->search_id;
                if(empty($search_id)){
                    throw new \Exception('No search id found');
                }
                dump($search_id);
                $offset+=30;
                if($response->count<$page*$offset){
                    dump('Response count: '.$response->count.' Page: '.$page.' Next Offset: '.$offset.' Break the loop');
                    break;
                }
            }

            // break while. // update time booking check
            $this->search->feeds()->updateExistingPivot($feed->id,['updated_at'=>now()]);
            if(!$found_hotels){
                throw new \Exception( 'Hotels is not found' );
            }
        } catch ( \Exception $e ) {
            $console->error( $e->getMessage() );
        }

    }

    /**
     * @param RapidApiBooking $booking
     * @param Search $search
     * @param string|null $search_id
     * @param int $offset
     *
     * @return array
     */
    protected function getParameters(RapidApiBooking $booking,Search $search,string $search_id=null,int $offset=0):array{
        $parameters = [
            'search_type'               => 'city',
            'offset'                    => $offset,
            'dest_ids'                  => $search->booking_dest_id,
            'guest_qty'                 => $search->number_of_adults,
            'arrival_date'              => $search->check_in_date->toDateString(),
            'departure_date'            => $search->check_in_date->addDays( $search->nights )->toDateString(),
            'room_qty'                  => 1,
            'price_filter_currencycode' => 'USD', //default.. override it if max_budget speciifed
            //'order_by'                  => 'price'
            //'order_by'                  => 'popularity'
            //'order_by'=>'class_descending'
        ];

        // order by: popularity|distance|class_descending|class_ascending|deals|review_score|price
        $categories_filter = [];
        if ( ! empty( $search->max_budget ) ) {
            //$price_per_night = (int)$this->search->max_budget;
            //$categories_filter[]='price::0-' . $price_per_night;
            //$categories_filter[]='price_category::' . $price_per_night;
            //$categories_filter[]='price_category::200';
            $categories_filter                       = array_merge( $categories_filter, $booking->getPriceCategories( (int) $search->max_budget ) );
            $parameters['price_filter_currencycode'] = $search->max_budget_currency;
            $parameters['order_by']                  = 'price'; // sort by price if no budget given
        } else {
            if ( ! empty( $search->rating ) ) {
                // sort by rating if no budget but rating given
                $parameters['order_by'] = 'review_score'; // sort by review_score if no budget given
            } else {
                // default sort by popularity
                $parameters['order_by'] = 'popularity'; // sort by popularity if no budget or rating given
            }
        }


        if ( ! empty( $search->hotel_class ) ) {
            $hotel_classes        = range( $search->hotel_class, 5 );
            $hotel_classes_filter = [];
            foreach ( $hotel_classes as $hotel_class ) {
                $hotel_classes_filter[] = 'class::' . $hotel_class;
            }

            $categories_filter[] = implode( ',', $hotel_classes_filter );
        }
        $categories_filter[]='property_type::204'; // hotels
        $categories_filter[]='facility::16'; // non smoking rooms
        $categories_filter[]='room_facilities::38'; // private bathrooms
        $categories_filter[]='out_of_stock::1'; // Only show available properties

        if ( ! empty( $categories_filter ) ) {
            $parameters['categories_filter'] = implode( ',', $categories_filter );
        }

        if ( ! empty( $search->children ) > 0 ) {
            $parameters['children_qty'] = count( $search->children );
            $parameters['children_age'] = implode( ',', $search->children );
        }
        return $parameters;
    }
}
