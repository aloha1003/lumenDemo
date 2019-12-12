<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PayChannel.
 *
 * @package namespace App\Models;
 */
class PayChannel extends Model implements Transformable
{
    use SoftDeletes;
    use TransformableTrait;
    const AVAILABLE_ENABLE = 1;
    const AVAILABLE_DISABLE = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'slug', 'available', 'comment', 'fee'];

}
