<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticDailyGameIncomeReport.
 *
 * @package namespace App\Models;
 */
class AnalyticDailyGameIncomeReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date',
        'game_name',
        'game_slug',
        'bet_people_number',
        'bet_number',
        'average_bet_number_per_round',
        'bet_gold',
        'average_bet_gold_per_round',
        'win_number',
        'average_win_number_per_round',
        'win_gold',
        'average_win_gold_per_round',
        'profit'
    ];

}
