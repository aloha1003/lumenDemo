<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserEditSexRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'sex' => 'bail|required|integer|in:0,1,2',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'sex' => __('message.sex'),
        ];
    }
}
