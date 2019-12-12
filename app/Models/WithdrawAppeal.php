<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WithdrawAppeal.
 *
 * @package namespace App\Models;
 */
class WithdrawAppeal extends Model implements Transformable
{
    use TransformableTrait;

    // 申訴處理状态
    const STATUS_WAIT = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAIL = 2;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'status',
        'withdraw_id',
        'transaction_no',
        'contact_info',
        'detail_info',
        'photo_url',
        'op_admin_id'
    ];

}
