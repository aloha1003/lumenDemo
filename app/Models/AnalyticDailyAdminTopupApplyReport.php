<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyAdminTopupApplyReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyAdminTopupApplyReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'topup_user_number',
        'topup_gold',
        'average_topup_gold',
    ];

}
