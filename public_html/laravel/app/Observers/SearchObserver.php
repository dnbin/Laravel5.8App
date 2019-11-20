<?php

namespace App\Observers;

use App\Jobs\SearchAmadeus;
use App\Jobs\SearchBooking;
use App\Jobs\SearchSendEntries;
use App\Models\Search;
use App\Notifications\NoOffersFound;
use App\Services\RapidApiBooking\RapidApiBooking;
use App\User;
use Illuminate\Log\Logger;

class SearchObserver
{
    protected $booking;
    protected $log;

    public function __construct(RapidApiBooking $booking,Logger $log){
        $this->log=$log;
        $this->booking=$booking;
    }

    public function creating(Search $search){
        $parameters = [
            'text'         => $search->city->name,
            'languagecode' => 'en-us'
        ];
        try {
            $dest_id = $this->booking->getDestId( 'city', $parameters );
            $search->booking_dest_id=$dest_id;
        }
        catch(\Exception $e){
            $search->booking_dest_id=null;
            $this->log->error($e->getMessage());
        }
    }

    /**
     * Handle the search "created" event.
     *
     * @param  Search  $search
     * @return void
     */
    public function created(Search $search)
    {
        ob_start();
        // chain jobs. save entries, booking check , amadeus check
        try {
            SearchBooking::withChain( [
                new SearchAmadeus($search),
                new SearchSendEntries( $search )
            ] )->dispatch( $search);
        }
        catch(\Exception $e){
            $this->log->error(get_class($this).': '.$e->getMessage());
        }
        ob_get_clean();

        if($search->entries->isEmpty()){
            /** @var User $user */
            $user=$search->user;
            $user->notify(new NoOffersFound($search));
        }
    }

    public function updating(Search $search){

        // get dest id on city change
        if($search->city->id!=$search->getOriginal('city_id')){
            $parameters = [
                'text'         => $search->city->name,
                'languagecode' => 'en-us'
            ];
            try {
                $dest_id = $this->booking->getDestId( 'city', $parameters );
                $search->booking_dest_id=$dest_id;
                $this->log->error('Booking DestId: '.$dest_id.' found');
            }
            catch(\Exception $e){
                $this->log->error($e->getMessage());
                $search->booking_dest_id=null;
            }
        }
    }

    /**
     * Handle the search "updated" event.
     *
     * @param  Search  $search
     * @return void
     */
    public function updated(Search $search)
    {
        // delete all attached entries if any search parameter changed.
        if($search->wasChanged([
            'city_id',
            'check_in_date' ,
            'nights',
            'hotel_class',
            'rating',
            'max_budget',
            'max_budget_currency',
            //'max_budget_discount',
            'number_of_adults',
            'children'
        ])){
            $search->entries()->detach();
        }

        //
        ob_start();
        SearchBooking::withChain( [
            new SearchAmadeus($search),
            new SearchSendEntries( $search )
        ] )->dispatch( $search);
        ob_get_clean();

        if($search->entries->isEmpty()){
            /** @var User $user */
            $user=$search->user;
            $user->notify(new NoOffersFound($search));
        }
    }

    /**
     * Handle the search "deleted" event.
     *
     * @param  Search  $search
     * @return void
     */
    public function deleted(Search $search)
    {
        //
    }

    /**
     * Handle the search "restored" event.
     *
     * @param  Search  $search
     * @return void
     */
    public function restored(Search $search)
    {
        //
    }

    /**
     * Handle the search "force deleted" event.
     *
     * @param  Search  $search
     * @return void
     */
    public function forceDeleted(Search $search)
    {
        //
    }
}
