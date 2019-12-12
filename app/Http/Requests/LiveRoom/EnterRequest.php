<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class EnterRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'password' => 'string|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            'password' => __('liveRoom.password'),
        ];
    }
}
