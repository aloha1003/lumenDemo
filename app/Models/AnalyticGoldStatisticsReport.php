<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticGoldStatisticsReport.
 *
 * @package namespace App\Models;
 */
class AnalyticGoldStatisticsReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'topup_gold',
        'purchase_gift_gold',
        'purchase_prop_gold',
        'purchase_barrage_gold',
        'game_bet_gold',
        'game_bet_win_gold',
        'remain_gold',
    ];

}
