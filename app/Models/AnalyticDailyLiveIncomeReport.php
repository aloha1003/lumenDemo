<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyLiveIncomeReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyLiveIncomeReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'total_recieve_gold',
        'average_recieve_gold',
        'purchase_prop_times',
        'prop_incom',
        'purchase_transport_times',
        'transport_incom',
    ];

}
