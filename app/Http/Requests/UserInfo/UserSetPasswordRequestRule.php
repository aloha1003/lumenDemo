<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserSetPasswordRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'new_password' => 'bail|required|size:32',
            'old_password' => 'bail|required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'new_password' => __('message.new_password'),
            'old_password' => __('message.old_password'),
        ];
    }

}
