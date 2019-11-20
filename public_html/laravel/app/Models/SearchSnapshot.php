<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SearchSnapshot
 * @package App\Models
 * @property int $id
 * @property int $search_id
 * @property array $snapshot
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class SearchSnapshot extends Model
{
    //
    public function search(){
        return $this->belongsTo(Search::class);
    }
}
