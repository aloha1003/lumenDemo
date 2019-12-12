<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyAnchorNumberReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyAnchorNumberReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'new_register_anchor_number',
        'all_anchor_number',
        'live_anchor_number',
        'live_number',
        'audience_number',
        'average_live_time'
    ];

}

