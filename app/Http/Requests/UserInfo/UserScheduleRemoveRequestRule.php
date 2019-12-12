<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserScheduleRemoveRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'schedule_id' => 'bail|integer|required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'schedule_id' => __('liveSchedule.scheduleId'),
        ];
    }

}
