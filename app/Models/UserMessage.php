<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserMessage.
 *
 * @package namespace App\Models;
 */
class UserMessage extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;

    const STATUS_UNREAD = 0;
    const STATUS_READ = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'title', 'content', 'status'];

}
