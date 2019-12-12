<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class ForgetPasswordValidateRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => 'bail|required|string',
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
