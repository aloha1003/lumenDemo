<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class SmsRegisterCode.
 *
 * @package namespace App\Models;
 */
//TODO
class SmsRegisterCode extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;

    const CONFIRM_WAIT = 0;
    const CONFIRM_SUCCESSED = 1;
    const CONFIRM_FAILED = 2;

    public $table = 'sms_register_code';
    public $fillable = ['cellphone', 'code', 'confirm'];

}
