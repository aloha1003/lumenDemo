<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserFollowCheckRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'target_user_id' => 'bail|required|digits:8',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'target_user_id' => __('message.target_user_id'),
        ];
    }

}
