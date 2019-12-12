<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserDailyWithdrawTimes.
 *
 * @package namespace App\Models;
 */
class UserDailyWithdrawTimes extends Model implements Transformable
{
    use TransformableTrait;

    const DAILY_WITHDRAW_TIMES = 3;
    const MAX_WITHDRAW_RMB = 100000;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'times',
    ];

}
