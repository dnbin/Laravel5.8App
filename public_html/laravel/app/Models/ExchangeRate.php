<?php

namespace App\Models;

use App\Models\Feed;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ExchangeRate
 * @package App\Services
 * @property int $id
 * @property Carbon $date
 * @property int $feed_id
 * @property string $base_currency
 * @property string $currency
 * @property float $rate
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class ExchangeRate extends Model
{
    //
    protected $guarded=['id'];
    protected $casts=['date'=>'date'];

    public function feed(){
        return $this->belongsTo(Feed::class);
    }

    public function scopeToday(Builder $query){
        return $query->where('date',Carbon::now()->toDateString());
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    /*
    public function scopeLatestDate(Builder $query){
        return $query->whereRaw('DATE=(SELECT MAX(date) FROM exchange_rates WHER feed_id='.$this->feed->id);
        //return $query->whereD;
    }
    */

}
