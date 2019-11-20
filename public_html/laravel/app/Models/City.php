<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class City
 * @package App\Models
 * @property int $id
 * @property $iata_code
 * @property string $name
 * @property int $country_id
 * @property float $lat
 * @property float $lng
 * @property int $population
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class City extends Model
{
    //
    protected $guarded=['id'];


    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function neighborhoods(){
        return $this->hasMany(Neighborhood::class);
    }

    public function searches(){
        return $this->hasMany(Search::class);
    }
}
