<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\SpecialAccountObserver;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SpecialAccount.
 *
 * @package namespace App\Models;
 */
class SpecialAccount extends Model implements Transformable
{
    use TransformableTrait;
    const CACHE_KEY = 'SpecialAccount';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['account', 'gold', 'user_id', 'set_at'];

    public static function boot()
    {
        parent::boot();
        static::observe(SpecialAccountObserver::class);
    }

    /**
     * 取得靓号key格式
     *
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T16:21:56+0800
     */
    public static function prettyIdKey($userId)
    {
        return self::CACHE_KEY . '_' . $userId;
    }
}
