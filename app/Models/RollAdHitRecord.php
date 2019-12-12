<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class RollAdHitRecord.
 *
 * @package namespace App\Models;
 */
class RollAdHitRecord extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'hit', 'roll_ad_id'];

}
