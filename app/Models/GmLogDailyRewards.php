<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmLogDailyRewards.
 *
 * @package namespace App\Models;
 */
class GmLogDailyRewards extends Model implements Transformable
{
    use TransformableTrait;
    protected $isLog = false;

    public $table = 'log_daily_rewards';
    public $timestamps = false;
    protected $connection = 'mysql_game';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'gid', 'rewards', 'totals', 'ctime', 'straight'];

}
