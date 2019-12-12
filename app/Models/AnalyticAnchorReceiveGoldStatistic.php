<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticAnchorReceiveGoldStatistic.
 *
 * @package namespace App\Models;
 */
class AnalyticAnchorReceiveGoldStatistic extends Model implements Transformable
{
    use TransformableTrait;

    public $table = 'analytic_anchor_receive_gold_statistic';

    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'anchor_id',
        'give_gift_user_id',
        'total_give_gift_gold'
    ];

}
