<?php

namespace App\Http\Requests\Register;

use App\Http\Requests\RuleRequest;

class SmsValidateRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => 'bail|required|digits:11',
            'sms_code' => 'bail|required|digits:6'
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'cellphone' => __('message.cellphone'),
            'sms_code' => __('message.sms_code'),
        ];
    }

}
