<?php

namespace App\Observers;

use App\Models\Entry;
use App\Models\Feed;
use App\Services\ConsoleColor;
use Carbon\Carbon;
use Illuminate\Log\Logger;

class EntryObserver {
    protected $log;
    protected $console;

    public function __construct( Logger $log, ConsoleColor $console ) {
        $this->log     = $log;
        $this->console = $console;
    }


    public function creating(Entry $entry){
        /** @var Feed $expedia_feed */
        $expedia_feed=Feed::where('name','expedia')->first();
        if(!empty($expedia_feed)){
            $expedia_hotel=$expedia_feed->entries()->where('title','like',$entry->title.'%')->where('country',$entry->country)->first();
            if(!empty($expedia_hotel)){
                $this->log->info('Expedia matched hotel is found. Hotel: '.$expedia_hotel->title.' Price: '.$expedia_hotel->price.' '.$expedia_hotel->currency );
                $entry->regular_room_rate_amount=$expedia_hotel->price;
                $entry->regular_room_rate_currency=$expedia_hotel->currency;
            }
            else{
                $this->log->error('Expedia matched hotel to '.$entry->ttile.' is not found');
            }
        }
        else{
            $this->log->critical('Expedia feed is not found. Entry Observer creating.');
        }
    }

    /**
     * Handle the entry "created" event.
     *
     * @param Entry $entry
     *
     * @return void
     */
    public function created( Entry $entry ) {

    }

    /**
     * Handle the entry "updated" event.
     *
     * @param Entry $entry
     *
     * @return void
     */
    public function updated( Entry $entry ) {
        // check entry changes.
        /*
         * For the same hotel, if the price hasn't been changed since the offer was sent, do not send it again.
         * For the same hotel, if the price has increased, do not send it again.
         * For the same hotel, if the price has decreased by more than 1%, it is OK to send the offer
         * For instance, the system finds hotel XYZ at $100, which is below the budget, include hotel XYZ in the offer email.
         * Later, when the system finds hotel XYZ at $100 two days later, do not send XYZ offer, since the price hasn't been changed
         * If XYZ has decreased to $90, it is OK to include XYZ in a new offer email
         * Later, when XYZ increased from $90 back to $100, do not send XYZ
         * If XYZ was at $100, and decreased to $99, there is no need to send it again. This is probably rare.
         */
        /*
        $original_price = is_null( $entry->getOriginal( 'price' ) ) ? null : (float) $entry->getOriginal( 'price' );

        $should_send_to_customer = false;

        if ( $entry->price !== $original_price ) {
            // price changed
            // negative = price increased, positive - price decreased
            $percentage = ( $original_price - $entry->price ) / $original_price * 100;

            if ( $percentage > 1 ) {
                // price was decreased >=1
                $should_send_to_customer = true;
                $this->console->warning('Percentage decreased: '.$percentage.' New price: '.$entry->price.' Old price: '.$original_price);
            } else {
                // do not send if price increased
            }
        }
        else{
            // price is equal. do not mark offer as new.
        }

        // mark entry as not sent. will send it to user again.
        if ( $should_send_to_customer ) {
            foreach ( $entry->searches as $search ) {
                //$this->console->warning('Update pivot is_sent=0 for entry #'.$entry->id.' Search Id: '.$search->id.' Old Price: '.$original_price.' New Price: '.$entry->price);
                //dump('update pivot is_sent=0 for entry #'.$entry->id.' Search Id: '.$search->id);
                $this->log->info( 'Mark entry as should_send_to_customer. Set pivot.sent_at=null for entry #' . $entry->id . ' Search Id: ' . $search->id . ' Old Price: ' . $original_price . ' New Price: ' . $entry->price );
                $entry->searches()->updateExistingPivot( $search->id,
                    [
                        'sent_at'    => null,
                    ]
                );
            }
        }
        */
    }

    /**
     * Handle the entry "deleted" event.
     *
     * @param Entry $entry
     *
     * @return void
     */
    public function deleted( Entry $entry ) {
        //
    }

    /**
     * Handle the entry "restored" event.
     *
     * @param Entry $entry
     *
     * @return void
     */
    public function restored( Entry $entry ) {
        //
    }

    /**
     * Handle the entry "force deleted" event.
     *
     * @param Entry $entry
     *
     * @return void
     */
    public function forceDeleted( Entry $entry ) {
        //
    }
}
