<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class LiveBarrageStatistics.
 *
 * @package namespace App\Models;
 */
class LiveBarrageStatistics extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'room_id',
        'anchor_id',
        'barrage_id',
        'barrage_name',
        'barrage_price',
        'count',
    ];

}
