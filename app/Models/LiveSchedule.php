<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class LiveSchedule.
 *
 * @package namespace App\Models;
 */
class LiveSchedule extends Model implements Transformable
{
    use TransformableTrait;

    const MAX_SCHEDULE_NUMBER = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'time',
    ];

}
