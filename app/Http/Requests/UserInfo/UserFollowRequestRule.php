<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserFollowRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'follow_user_id' => 'bail|required|digits:8',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'follow_user_id' => __('message.follow_user_id'),
        ];
    }

}
