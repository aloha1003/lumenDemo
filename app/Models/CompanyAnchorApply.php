<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyAnchorApply.
 *
 * @package namespace App\Models;
 */
class CompanyAnchorApply extends Model implements Transformable
{
    use TransformableTrait;

    const WAIT_STATUS = 0;
    const FAIL_STATUS = 1;
    const SUCCESS_STATUS = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
        'real_name',
        'no',
        'cellphone',
        'alipay_account',
        'photo',
        'password',
        'comment',
        'manager_id',
        'company_id',
        'op_admin_id',
        'op_admin_comment',
    ];

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
