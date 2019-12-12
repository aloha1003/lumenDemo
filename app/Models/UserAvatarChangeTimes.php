<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserAvatarChangeTimes.
 *
 * @package namespace App\Models;
 */
class UserAvatarChangeTimes extends Model implements Transformable
{
    use TransformableTrait;
    //TODO
    const AVATAR_CHANGE_TIMES = 3;
    public $table = 'user_avatar_change_times';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'number'];

}
