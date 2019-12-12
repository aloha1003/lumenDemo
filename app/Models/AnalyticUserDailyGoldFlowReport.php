<?php

namespace App\Models;


use App\Models\BaseModel;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticUserDailyGoldFlowReport.
 *
 * @package namespace App\Models;
 */
class AnalyticUserDailyGoldFlowReport extends BaseModel implements Transformable
{
    use TransformableTrait;

    public $table = 'analytic_user_daily_gold_flow_report';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'date',
        'gold',
        'in_gold',
        'out_gold',
        'purchase_out_gold',
        'agent_out_gold',
        'other_out_gold',
        'receive_in_gold',
        'agent_in_gold',
        'store_in_gold',
        'other_in_gold'
    ];
    
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

}
