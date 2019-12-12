<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\UserInfoObserver;
use App\Models\RealNameVerify;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserConfig.
 *
 * @package namespace App\Models;
 */
class UserConfig extends Model implements Transformable
{
    use TransformableTrait;
    public $table = "user_config";

    //是否被封号
    const IS_BLOCK_YES = 1;
    const IS_BLOCK_NO = 0;
    //是否是代理
    const IS_AGENT_YES = 1;
    const IS_AGENT_NO = 0;
    //是否可以使用弹幕
    const CAN_USE_BARRAGE_YES = 1;
    const CAN_USE_BARRAGE_NO = 0;
    //是否可以使用传送门
    const CAN_USE_TRANSFER_YES = 1;
    const CAN_USE_TRANSFER_NO = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'is_verify_real_name', 'is_agent', 'is_lock', 'chat_ban_datetime', 'assign_agent_date', 'assign_agent_user_id', 'block_reason', 'unblock_reason', 'operate_reason'];

    public static function boot()
    {
        parent::boot();
        static::observe(UserInfoObserver::class);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function userAuth()
    {
        return $this->hasOne('App\Models\UserAuth', 'user_id', 'user_id');
    }

    public function real_name_verify()
    {
        return $this->hasOne(RealNameVerify::class, 'user_id', 'user_id');
    }

    public function assignAgentUser()
    {
        return $this->belongsTo('App\Models\User', 'assign_agent_user_id');
    }

    public static function blockOptions()
    {
        return modelColumn('UserConfig')['is_block_options'];
    }
}
