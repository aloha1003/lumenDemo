<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Models\Observers\GameReleaseVersionObserver;

/**
 * Class GameRelease.
 *
 * @package namespace App\Models;
 */
class GameRelease extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_slug',
        'version',
        'is_on',
        'local_download_url',
        'comment'
    ];

    public static function boot()
    {
        parent::boot();
        static::observe(GameReleaseVersionObserver::class);
    }
}
