<?php

namespace App\Models;

use App\Models\BaseModel as Model;

class SmsCode extends Model
{
    const TYPE_REGISTER = 0;
    const TYPE_LOGIN = 1;
    const TYPE_CHANGE_PASSWORD = 2;

    const CODE_WAIT = 0;
    const CODE_SUCCESSED = 1;
    const CODE_FAILED = 2;

    const SEND_TYPE_ODRINARY = 0;
    const SEND_TYPE_MARKETING = 1;

    public $table = 'sms_code';
    public $fillable = ['type', 'cellphone', 'code', 'confirm'];

}
