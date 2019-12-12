<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserEditSignRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'sign' => 'bail|nullable|string|max:30',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'sign' => __('message.sign_status'),
        ];
    }
}
