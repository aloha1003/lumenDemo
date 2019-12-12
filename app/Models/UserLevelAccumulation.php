<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserLevelAccumulation.
 *
 * @package namespace App\Models;
 */
class UserLevelAccumulation extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'bet_gold_for_next_exp',
        'given_gold_for_next_exp',
        'receive_gold_for_next_exp',
        'topup_rmb_for_next_exp'
    ];

}
