<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class LiveGiftStatistics.
 *
 * @package namespace App\Models;
 */
class LiveGiftStatistics extends Model implements Transformable
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
        'gift_type_slug',
        'gift_name',
        'gift_price',
        'count',
    ];

}
