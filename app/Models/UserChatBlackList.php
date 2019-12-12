<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserChatBlackList.
 *
 * @package namespace App\Models;
 */
class UserChatBlackList extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'user_chat_black_list';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'black_user_id'];

}
