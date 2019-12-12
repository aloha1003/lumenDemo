<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserRetentionRate.
 *
 * @package namespace App\Models;
 */
class UserRetentionRate extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['one_day_num', 'three_day_num', 'seven_day_num', 'fourteen_day_num', 'thirty_day_num', 'sixty_day_num', 'date', 'base_num'];

}
