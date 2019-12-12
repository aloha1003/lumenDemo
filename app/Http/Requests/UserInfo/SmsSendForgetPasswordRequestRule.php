<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class SmsSendForgetPasswordRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => 'bail|required|string',
            'is_resend' => 'bail|nullable|in:0,1',
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
        ];
    }

}
