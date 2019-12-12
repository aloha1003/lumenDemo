<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\Sortable;
use App\Models\EloquentSortable\SortableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Annouce.
 *
 * @package namespace App\Models;
 */
class Annouce extends Model implements Transformable, Sortable
{
    use TransformableTrait;
    use SortableTrait;

    // 預設類型
    const DEFAULT_TYPE_SLUG = "Notice";

    public $sortable = [
        'order_column_name' => 'weight',
        'sort_when_creating' => true,
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['type_slug', 'title', 'platform', 'admin_id', 'content', 'status', 'start_at', 'finish_at', 'weight', 'is_read', 'is_close'];

    // 显示平台类型
    const PLATFORM_NONE = 0;
    const PLATFORM_IOS = 1;
    const PLATFORM_ANDROID = 2;
    const PLATFORM_ALL = 3;
    //显示状态
    const STATUS_NO = 0;
    const STATUS_YES = 1;
}
