<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserFollow.
 *
 * @package namespace App\Models;
 */
class UserFollow extends Model implements Transformable
{
    use TransformableTrait;

    public $table = 'user_follow';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'follow_uid'];

}
