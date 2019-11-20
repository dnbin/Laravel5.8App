<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Neighborhood
 * @package App\Models
 * @property int $id
 * @property int $city_id
 * @property string $name
 * @property float $lat
 * @property float $lng
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class Neighborhood extends Model
{
    //
    protected $guarded=['id'];

    public function city(){
        return $this->belongsTo(City::class);
    }

    public function searches(){
        return $this->belongsToMany(Search::class);
    }
}
