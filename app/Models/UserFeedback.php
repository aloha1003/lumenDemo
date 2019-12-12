<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserFeedback.
 *
 * @package namespace App\Models;
 */
class UserFeedback extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = "user_feedback";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'contact_info', 'feedback_info', 'type_slug'];

}
