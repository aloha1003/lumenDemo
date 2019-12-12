<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class EndInfoRequest extends RuleRequest
{

    public function rules()
    {
        return [
             'room_id' => 'bail|required|numeric',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            //'password' => __('liveRoom.password'),
        ];
    }
}
