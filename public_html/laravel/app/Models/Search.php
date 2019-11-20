<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

/**
 * Class Search
 * @package App\Models
 * @property int $id
 * @property int $user_id
 * @property int $city_id
 * @property int $booking_dest_id
 * @property Carbon $check_in_date
 * @property int $nights
 * @property int $hotel_class
 * @property float $rating
 * @property float $max_budget
 * @property string $max_budget_currency
 * @property int $max_budget_discount
 * @property int $number_of_adults
 * @property array $children
 * @property Carbon $hotel_offers_sent_at
 * @property string $frequency
 * @property string $ip
 * @property string $referrer
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Search extends Model {
    //
    protected $guarded = [ 'id' ];
    protected $appends = [ 'delete_url', 'view_url', 'update_url', 'unsubscribe_url' ];
    protected $casts = [ 'check_in_date' => 'date', 'hotel_offers_sent_at' => 'datetime', 'children' => 'array' ];

    // save model without  events (inc observable)
    public function saveQuietly( array $options = [] ) {
        return static::withoutEvents( function () use ( $options ) {
            return $this->save( $options );
        } );
    }

    public function getDeleteUrlAttribute() {
        return route( 'searches.search.delete', [ 'id' => $this->id ] );
    }

    public function getViewUrlAttribute() {
        return route( 'searches.search.view', [ 'id' => $this->id ] );
    }

    public function getPublicViewUrlAttribute() {
        return URL::temporarySignedRoute(
            'searches.search.view.public', now()->addWeek(), [ 'id' => $this->id ]
        );
    }

    public function getUpdateUrlAttribute() {
        return route( 'searches.search.update', [ 'id' => $this->id ] );
    }

    public function getUnsubscribeUrlAttribute() {
        return URL::temporarySignedRoute(
            'searches.search.unsubscribe', now()->addDay(), [ 'id' => $this->id ]
        );
    }


    public function user() {
        return $this->belongsTo( User::class );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entries() {
        return $this->belongsToMany( Entry::class )->using(EntrySearch::class)->withPivot( [ 'sent_at','sent_snapshot' ] )->withTimestamps();
    }

    public function scopeActive( Builder $query ) {
        return $query->where( 'status', 1 );
    }

    public function scopeExpired( Builder $query ) {
        return $query->where( 'check_in_date', '<', Carbon::now()->toDateString() );
    }

    public function city() {
        return $this->belongsTo( City::class );
    }

    public function neighborhoods(){
        return $this->belongsToMany(Neighborhood::class);
    }

    public function feeds(){
        return $this->belongsToMany(Feed::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function snapshots(){
        return $this->hasMany(SearchSnapshot::class);
    }
}
