<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class TencentLeaveGroupRequestRule extends RuleRequest
{
    public function rules()
    {
        return [
            'SdkAppid' => 'string|required',
            'CallbackCommand' => 'string|required',
            // 'GroupId' => 'string|required',
            // 'Type' => 'string|required',
            // 'ExitType' => 'string|required',
            // 'Operator_Account' => 'string|required',
            // 'ExitMemberList' => 'array|required'
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

}
