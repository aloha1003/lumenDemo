<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BlockDevice.
 *
 * @package namespace App\Models;
 */
class BlockDevice extends Model implements Transformable
{
    use TransformableTrait;
    //是否封锁
    const IS_BLOCK_YES = 1;
    const IS_BLOCK_NO = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'ip', 'user_id', 'channel', 'uuid', 'is_block'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
