<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserLoginRecord.
 *
 * @package namespace App\Models;
 */
class UserLoginRecord extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['device_type', 'user_id', 'ip'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

}
