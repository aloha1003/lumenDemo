<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserEditBirthdayRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'birthday' => 'bail|required|date_format:Y-m-d|before:today',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'birthday' => __('message.birthday'),
        ];
    }
}
