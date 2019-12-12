<?php

namespace App\Http\Requests\Login;

use App\Http\Requests\RuleRequest;

class LoginIdRequestRule extends RuleRequest
{
    public function rules()
    {
        return [
            'user_id' => 'bail|required',
            'password' => 'bail|required',
            //'device_id' => 'bail|required',
            'os_version' => 'bail|required|in:android,ios',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'user_id' => __('message.user_id'),
            'password' => __('message.password'),
            //'device_id' => __('message.device_id'),
            'os_version' => __('message.os_version'),
        ];
    }
}
