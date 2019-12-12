<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserSetPasswordBySmsRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'password' => 'bail|required',
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
            'password' => __('message.password'),
            'sms_code' => __('message.sms_code'),
        ];
    }

}
