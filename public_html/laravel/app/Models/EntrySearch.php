<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class EntrySearch
 * @package App\Models
 * @property Carbon $sent_at
 * @property array $sent_snapshot
 * @property boolean $is_latest
 * @property Carbon $updated_at
 * @property Carbon $created_at
 */
class EntrySearch extends Pivot
{
    //
    protected $casts=['sent_at'=>'datetime','is_latest'=>'boolean','sent_snapshot'=>'array'];
}
