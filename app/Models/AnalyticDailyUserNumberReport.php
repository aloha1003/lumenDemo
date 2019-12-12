<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyUserNumberReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyUserNumberReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'new_register_user_number',
        'login_user_number',
        'max_online_user_number',
        'login_times',
        'last_30_day_login_user_number',
        'adhesive'
    ];

}
