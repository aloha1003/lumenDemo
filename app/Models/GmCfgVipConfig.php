<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GmCfgVipConfig.
 *
 * @package namespace App\Models;
 */
class GmCfgVipConfig extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'cfg_vip_config';
    public $timestamps = false;
    protected $connection = 'mysql_game';
    protected $isLog = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'name', 
        'level', 
        'diamond', 
        'exchangecoin', 
        'rewardcoin', 
        'expire_at', 
        'checkinreward', 
        'msgcolor', 
        'cardbg', 
        'medal', 
        'enter_efficacy', 
        'day_login_active', 
        'active_reward', 
        'renewal_discount', 
        'chat', 
        'modify_avatar', 
        'online_notice', 
        'personal_service', 
        'custom_avatar', 
        'room_enter', 
        'hundredroom_enter', 
        'hundredroom_redpacket', 
        'buynotice', 
        'description', 
        'modify_at', 
        'viptype', 
        'status', 
        'send_props', 
        'safebox_max'
    ];

}
