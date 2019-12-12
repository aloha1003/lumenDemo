<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Models\Observers\MaintainObserver;

/**
 * Class Maintain.
 *
 * @package namespace App\Models;
 */
class Maintain extends Model implements Transformable
{
    use TransformableTrait;
    const CACHE_KEY = 'maintain_config';

    const MAINTAIN_ID = 1;

    const MAINTAIN_SWITCH_ON = 1;
    const MAINTAIN_SWITCH_OFF = 0;

    const DATE_MODE_SWITCH_ON = 1;
    const DATE_MODE_SWITCH_OFF = 0;

    public $table = 'maintain';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'switch',
        'date_switch',
        'start_datetime',
        'end_datetime',
        'front_comment',
        'comment',
    ];
    public static function boot()
    {
        parent::boot();
        static::observe(MaintainObserver::class);
    }

}
