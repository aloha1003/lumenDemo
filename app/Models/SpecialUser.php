<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SpecialUser.
 *
 * @package namespace App\Models;
 */
class SpecialUser extends Model implements Transformable
{
    use TransformableTrait;
    //用户类型
    const USER_TYPE_NORMAL = 1;
    const USER_TYPE_TEST = 2;
    const USER_TYPE_ROBOT = 3;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user_id', 'user_type'];

}
