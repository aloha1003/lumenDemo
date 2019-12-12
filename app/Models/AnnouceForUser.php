<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnnouceForUser.
 *
 * @package namespace App\Models;
 */
class AnnouceForUser extends Model implements Transformable
{
    use TransformableTrait;

    // 預設類型
    const DEFAULT_TYPE_SLUG = "Notice";
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type_slug',
        'title',
        'content',
        'admin_id',
        'is_read',
        'is_close'
    ];

}
