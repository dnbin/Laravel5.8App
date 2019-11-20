<?php

namespace App\Models;

use App\Models\ExchangeRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class Feed
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $feed_path
 * @property bool $status
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Feed extends Model
{
    //
	protected $guarded=['id'];
	protected $casts=['status'=>'boolean'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function entries(){
		return $this->hasMany(Entry::class);
	}

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
	public function exchangeRates(){
	    return $this->hasMany(ExchangeRate::class);
    }

    public function searches(){
	    return $this->belongsToMany(Search::class);
	}

}
