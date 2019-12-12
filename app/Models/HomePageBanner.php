<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\Sortable;
use App\Models\EloquentSortable\SortableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class HomePageBanner.
 *
 * @package namespace App\Models;
 */
class HomePageBanner extends Model implements Transformable, Sortable
{
    use TransformableTrait;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'weight',
        'sort_when_creating' => true,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'cover', 'platform', 'admin_id', 'content', 'status', 'start_at', 'finish_at', 'weight', 'target', 'href'];
    // 连结外跳方式
    const TARGET_BLANK = 'blank'; //外跳
    // 显示平台类型
    const PLATFORM_NONE = 0;
    const PLATFORM_IOS = 1;
    const PLATFORM_ANDROID = 2;
    const PLATFORM_ALL = 3;
    //显示状态
    const STATUS_NO = 0;
    const STATUS_YES = 1;

    //档案上传目录
    const COVER_PATH_PREFIX = 'homepage_banner';

    public function getCoverAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }

}
