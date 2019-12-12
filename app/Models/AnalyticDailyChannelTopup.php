<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyChannelTopup.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyChannelTopup extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'payment_channel_slug',
        'payment_channel_name',
        'topup_user_number',
        'topup_rmb',
        'topup_rate',
        'average_topup_rmb',
    ];

}
