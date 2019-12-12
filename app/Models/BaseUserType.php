<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BaseUserType.
 *
 * @package namespace App\Models;
 */
class BaseUserType extends Model implements Transformable
{
    use TransformableTrait;
    public $table = 'base_user_type';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];

}
