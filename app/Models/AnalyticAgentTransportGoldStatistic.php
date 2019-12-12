<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticAgentTransportGoldStatistic.
 *
 * @package namespace App\Models;
 */
class AnalyticAgentTransportGoldStatistic extends Model implements Transformable
{
    use TransformableTrait;


    public $table = 'analytic_agent_transport_gold_statistic';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'agnet_id',
        'tranport_gold_user_id',
        'total_transport_gold'
    ];

}
