<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use App\Models\Observers\AppReleaseVersionObserver;

/**
 * Class Channel.
 *
 * @package namespace App\Models;
 */
class Channel extends Model implements Transformable
{
    use TransformableTrait;

    public static function boot()
    {
        parent::boot();
        static::observe(AppReleaseVersionObserver::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'key_code',
        'official_url',
        'ios_official_download_url',
        'ios_official_download_cdn_url',
        'android_official_download_url',
        'android_official_download_cdn_url',
        'comment',
    ];

}
