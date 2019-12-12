<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ModelLog.
 *
 * @package namespace App\Models;
 */
class ModelLog extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['model_name', 'model_primary_key_column', 'model_id', 'ip', 'user_id', 'origin_data', 'diff_data', 'env', 'update_source'];

}
