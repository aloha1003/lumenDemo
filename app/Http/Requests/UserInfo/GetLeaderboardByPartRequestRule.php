<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class GetLeaderboardByPartRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'type' => 'bail|required|in:anchor,fans',
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
            'type' => __('message.type'),
            'date_range' => __('message.date_range'),
        ];
    }

}
