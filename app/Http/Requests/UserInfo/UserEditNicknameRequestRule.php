<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserEditNicknameRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'nickname' => 'bail|required|string|max:20',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'nickname' => __('message.nickname'),
        ];
    }
}
