<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyCompanyWithdrawReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyCompanyWithdrawReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'company_id',
        'company_name',
        'withdraw_rmb',
    ];

}
