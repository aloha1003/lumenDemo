<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\SystemConfigObserver;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SystemConfig.
 *
 * @package namespace App\Models;
 */
class SystemConfig extends Model implements Transformable
{
    use TransformableTrait;
    const CACHE_KEY = 'SYS_CONFIG';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['slug', 'value', 'title', 'comment'];
    public function getValueAttribute($value)
    {
        $jsonValue = json_decode($value, true);
        if (json_last_error()) {
            $jsonValue = $value;
        }
        return $jsonValue;
    }
    public static function boot()
    {
        parent::boot();
        static::observe(SystemConfigObserver::class);
    }

}
