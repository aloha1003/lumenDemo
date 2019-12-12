<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserScheduleAddRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'time' => 'bail|date|required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'time' => __('message.time'),
        ];
    }

}
