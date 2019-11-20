<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Country
 * @package App\Models
 * @property int $id
 * @property string $name
 * @property string $alpha_2
 * @property string $alpha_3
 * @property string $coutnry_code
 * @property string $iso_3166_2
 * @property string $region
 * @property string $intermediate_region
 * @property string $region_code
 * @property string $sub_region_code
 * @property string $intermediate_region_code
 */
class Country extends Model
{
    //
    protected $guarded=['id'];

    public function cities(){
        return $this->hasMany(City::class);
    }
}
