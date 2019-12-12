<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserLeaderboardInfoByPartRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'target_user_id' => 'bail|digits:8',
            'date_range' => 'bail|required|in:day,week,month,all',
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
            'date_range' => __('message.date_range'),
        ];
    }

}
