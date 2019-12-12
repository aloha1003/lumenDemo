<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Models\Observers\BaseLevelObserver;

/**
 * Class BaseLevel.
 *
 * @package namespace App\Models;
 */
class BaseLevel extends Model implements Transformable
{
    use TransformableTrait;

    const CACHE_KEY = 'BASE_LEVEL';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'lv',
        'exp',
        'exp_sum',
        'privilege'
    ];
    
    public static function boot()
    {
        parent::boot();
        static::observe(BaseLevelObserver::class);
    }

}
