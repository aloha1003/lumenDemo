<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserTopupOrderLog.
 *
 * @package namespace App\Models;
 */
class UserTopupOrderLog extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['transaction_no', 'pay_step', 'origin_data', 'diff_data', 'env', 'user_id', 'ip', 'payload'];

}
