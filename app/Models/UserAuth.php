<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\AuthCacheObserver;
use App\Model\Auth\User as Authenticatable;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserAuth.
 *
 * @package namespace App\Models;
 */
class UserAuth extends Authenticatable implements Transformable
{
    use TransformableTrait;

    public $table = 'user_auth';
    public $fillable = ['user_id', 'password', 'cellphone', 'wechat', 'qq', 'weibo'];
    protected $hidden = ['password'];
    //TODO 改写到 系统参数
    const SALT = 'Eq4q8USE';

    public static function passwordEncry($raw)
    {
        return (md5(md5($raw) . self::SALT));
    }

    public static function boot()
    {
        parent::boot();
        self::observe(AuthCacheObserver::class);
    }
}
