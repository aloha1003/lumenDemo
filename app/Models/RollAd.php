<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\Sortable;
use App\Models\EloquentSortable\SortableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class RollAd.
 *
 * @package namespace App\Models;
 */
class RollAd extends Model implements Transformable, Sortable
{
    use TransformableTrait;
    use SortableTrait;
    use SoftDeletes;
    public $sortable = [
        'order_column_name' => 'weight',
        'sort_when_creating' => true,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'platform', 'target', 'status', 'cover', 'href', 'content', 'start_at', 'weight', 'finish_at'];
    // 显示平台类型
    const PLATFORM_NONE = 0;
    const PLATFORM_IOS = 1;
    const PLATFORM_ANDROID = 2;
    const PLATFORM_ALL = 3;
    // 连结外跳方式
    const TARGET_BLANK = 'blank'; //外跳
    //显示状态
    const STATUS_NO = 0;
    const STATUS_YES = 1;
    //档案上传目录
    const COVER_PATH_PREFIX = 'roll_ad';
    protected $appends = ['hit_url'];
    public function getCoverAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }

    public function getHitUrlAttribute()
    {
        // return route('api.ad.hit', ['id' => $this->id]);
        return $this->href;
    }

    public function records()
    {
        return $this->hasMany('App\Models\RollAdHitRecord', 'roll_ad_id', 'id');
    }
}
