<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyUserWithdrawReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyUserWithdrawReport extends Model implements Transformable
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
        'withdraw_user_number',
        'withdraw_rmb',
        'average_withdraw_rmb',
    ];

}
