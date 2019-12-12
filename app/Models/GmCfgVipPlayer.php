<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmCfgVipPlayer.
 *
 * @package namespace App\Models;
 */
class GmCfgVipPlayer extends Model implements Transformable
{
    use TransformableTrait;

    public $table = "cfg_vip_player";
    public $timestamps = false;
    protected $connection = 'mysql_game';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'userid', 'vipid', 'vip_type', 'start_at', 'expire_day', 'is_use', 'expire_at', 'is_update', 'pause_at'];

}
