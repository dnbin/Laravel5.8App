<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

/**
 * Class Entry
 * @package App\Models
 * @property int $id
 * @property int $feed_id
 * @property int $feed_entry_id
 * @property string $title
 * @property string $description
 * @property string $travel_type
 * @property string $street_address
 * @property string $city
 * @property string $province_state
 * @property int $zip_code
 * @property string $country
 * @property string $phone_number
 * @property float $latitude
 * @property float $longitude
 * @property float $price
 * @property string $currency
 * @property float $regular_room_rate_amount
 * @property string $regular_room_rate_currency
 * @property float $saving_full_price
 * @property int $saving_percentage
 * @property string $link
 * @property string $image_link
 * @property float $star_rating
 * @property float $review_score
 * @property float $location_score
 * @property string $custom_label_0
 * @property string $custom_label_1
 * @property int $bedrooms
 * @property int $baths
 * @property Carbon $last_updated_at
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Entry extends Model {
    //
    protected $guarded = [ 'id' ];
    protected $casts = [ 'last_updated_at' => 'datetime' ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function searches() {
        return $this->belongsToMany( Search::class )->using( EntrySearch::class )->withPivot( [
            'sent_at',
            'sent_snapshot'
        ] )->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function feed() {
        return $this->belongsTo( Feed::class );
    }

    public function linkFormatting( Search $search, Feed $feed ) {
        /*
        Add these parameters to the URL:
            checkin_monthday: check in day of the month, e.g. 7 for the 7th day of the month
            checkin_year_month: check in month, e.g. 2019-8 for August 2019
            checkout_monthday: check out day of the month, e.g. 8 for the 8th day of the month
            checkout_year_month: check out month, e.g. 2019-8 for August 2019
        */
        if ( empty( $this->link ) ) {
            return null;
        }
        if ( $feed->name === 'booking' ) {
            $data = [
                'checkin_monthday'    => $search->check_in_date->day,
                'checkin_year_month'  => $search->check_in_date->format( 'Y-m' ),
                'checkout_monthday'   => $search->check_in_date->addDays( $search->nights )->day,
                'checkout_year_month' => $search->check_in_date->addDays( $search->nights )->format( 'Y-m' )
            ];

            return $this->link . '?' . http_build_query( $data );
        } else {
            return $this->link;
        }
    }

    /**
     * @param Search $search
     * @param Feed $feed
     *
     * @return bool
     */
    public function searchValidate( Search $search, Feed $feed ): bool {
        // check if entry fit any searches. if yes attach it to search
        try {
            /** @var Search $search */

            if ( ! empty( $search->hotel_class ) && $this->star_rating < $search->hotel_class ) {
                throw new \Exception( 'Star Rating: ' . $this->star_rating . ' Search Requirement: ' . $search->hotel_class . '. Lower than expected.' );
            }

            if ( ! empty( $search->rating ) && $this->review_score < $search->rating && ! empty( $this->review_score ) ) {
                throw new \Exception( 'Review score: ' . $this->review_score . ' Search Requirement: ' . $search->rating . '. Lower than expected.' );
            }

            if ( $this->city != $search->city->name ) {
                throw new \Exception( 'Wrong city' );
            }

            if ( empty( $this->price ) ) {
                throw new \Exception( 'Skip hotels without price' );
            }

            if ( ! empty( $search->max_budget ) ) {
                //$max_price_per_night = round( $search->max_budget / $search->nights, 2 );
                $max_price_per_night = $search->max_budget;

                if ( $search->max_budget_currency !== $this->currency ) {
                    /** @var Collection $rates */
                    $rates = $feed->exchangeRates()
                                  ->where( 'date', '>=',Carbon::now()->subDay()->toDateString() )
                                  ->orderBy( 'date', 'desc' )
                                  ->get();
                    if ( $rates->isNotEmpty() ) {
                        $rate = $rates->where( 'currency', $this->currency )->first();
                        if ( empty( $rate ) ) {
                            // skip entry if no rates found for given currency
                            throw new \Exception( 'No rate found for currency ' . $this->currency );
                        }
                        if ( empty( $this->price ) || ( ( $this->price / $rate->rate ) > $max_price_per_night ) ) {
                            throw new \Exception( 'Currency is not equal: ' . $search->max_budget_currency . '/' . $this->currency . ' Empty price or Price after Rate conversion is too high (' . ( $this->price / $rate->rate ) . ') ' );
                        }
                    } else {
                        throw new \Exception( 'No exchange rates found on ' . $search->max_budget_currency . '/' . $this->currency . ' Currency is not the same or price is too high' );
                    }
                } else {
                    // currency is the same
                    if ( empty( $this->price ) || $this->price > $max_price_per_night ) {
                        throw new \Exception( 'Currency is equal. Empty price (' . $this->price . ') or Price is too high (Entry price: ' . $this->price . ' Search per night: ' . $max_price_per_night . ') ' );
                    }
                }
            }

            // check regular rate agains search discount
            /*
            if(!empty($search->max_budget_discount)){

                //if(empty($this->regular_room_rate_amount)){
                  //  throw new \Exception('Looking for discount at '.$search->max_budget_discount.' but regular room rate is not found');
                //}

                if(empty($this->saving_percentage)){
                    throw new \Exception('Looking for discount at '.$search->max_budget_discount.' but saving percentage is not found');
                }

                $percentage=$this->saving_percentage;


                //if($this->regular_room_rate_currency===$this->currency){
                  //  // calculate percentage.
                    //$percentage = round(( $this->regular_room_rate_amount-$this->price) / $this->regular_room_rate_amount * 100,2);
                    //dump('Percentage is: '.$percentage.' Regular Room Rate: '.$this->regular_room_rate_amount.' Offer Price: '.$this->price );
                //}
                //else{
                  //  // find a exchange rate
                    //$rates = $feed->exchangeRates()
                      //            ->where( 'date', Carbon::now()->subDay()->toDateString() )
                        //          ->orderBy( 'date', 'desc' )
                          //        ->get();
                    //if ( $rates->isNotEmpty() ) {
//                            $offer_price_rate = $rates->where('base_currency','USD')->where('currency',$this->currency)->first();
//                          if ( empty( $offer_price_rate ) ) {
//                            // skip entry if no rates found for given currency
  //                          throw new \Exception( 'No rate found for offer currency USD/'.$this->currency );
    //                    }
//
//                          $percentage = round(( ($this->regular_room_rate_amount*$offer_price_rate->rate)-$this->price) / ($this->regular_room_rate_amount*$offer_price_rate->rate) * 100,2);
//                        dump('Percentage is: '.$percentage.' Regular Room Rate: '.$this->regular_room_rate_amount.' Offer Price: '.($this->price/$offer_price_rate->rate).'USD ('.$this->price.$this->currency.') Exchange Rate: '.$offer_price_rate->rate );
  //                  }
    //                else{
      //                  throw new \Exception('Exchange rate is not found for '.$this->currency.'/'.$this->regular_room_rate_currency);
        //            }
          //      }

                if($percentage<$search->max_budget_discount){
                    throw new \Exception('Discount percentage is too small: '.$percentage.' Should be at least: '.$search->max_budget_discount);
                }
            }
*/

            return true;
        } catch ( \Exception $e ) {
            // silent. entry doesn't fit this search
            dump( $e->getMessage() );

            return false;
        }
    }

    /**
     * @param float $price_per_night
     *
     * @throws \Exception
     */

    public function priceValidate( ?float $snapshot_price_per_night ) {
        if ( is_null( $this->price ) && is_null( $snapshot_price_per_night ) ) {
            // skip. no price change
            throw new \Exception( 'New && snapshot price is null. No change.' );
        }

        if ( $this->price != $snapshot_price_per_night ) {
            // price changed
            // negative = price increased, positive - price decreased
            $percentage = ( $snapshot_price_per_night - $this->price ) / $snapshot_price_per_night * 100;

            if ( $percentage > 3 ) {
                // price was decreased >=1
                dump( 'Valid Price. Update offer. Percentage decreased: ' . $percentage . ' New price: ' . $this->price . ' Old price: ' . $snapshot_price_per_night );
            } else {
                // do not send if price increased
                throw new \Exception( 'Price was increased from ' . $snapshot_price_per_night . ' to ' . $this->price . ' Invalid.' );
            }
        } else {
            // price is equal. no change
            throw new \Exception( 'Prices is equal.' );
        }

        return true;
    }
}
