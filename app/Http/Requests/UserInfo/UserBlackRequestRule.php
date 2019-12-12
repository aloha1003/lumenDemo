<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserBlackRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'black_user_id' => 'bail|required|digits:8',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'black_user_id' => __('message.black_user_id'),
        ];
    }

}
