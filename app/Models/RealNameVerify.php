<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class RealNameVerify.
 *
 * @package namespace App\Models;
 */
class RealNameVerify extends Model implements Transformable
{
    use TransformableTrait;
    const PHOTO_STORE_ROOT = 'realNamePhoto';
    public $table = 'real_name_verifies';

    const IS_CONFIRM_NO = 0; //尚未验证
    const IS_CONFIRM_APPLYING = 3; //验证处理中
    const IS_CONFIRM_PASS = 1; //验证通过
    const IS_CONFIRM_REJECT = 2; //验证失败
    const IS_CONFIRM_RESEND = 4; //重新送审
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['real_name', 'no', 'cellphone', 'alipay_account', 'photo', 'user_id', 'is_confirm'];
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function getPhotoAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }
}
