<?php

namespace App\Models;

use App\Models\Observers\AppReleaseVersionObserver;
use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AppRelease.
 *
 * @package namespace App\Models;
 */
class AppRelease extends Model implements Transformable
{
    use TransformableTrait;
    const ON_RELEASE = 1;
    public static function boot()
    {
        parent::boot();
        //写入修改log
        static::observe(AppReleaseVersionObserver::class);
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'channel_key_code',
        'ios_on',
        'android_on',
        'ios_local_download_url',
        'ios_version_number',
        'ios_version_code',
        'android_local_download_url',
        'android_version_number',
        'android_version_code',
    ];

}
