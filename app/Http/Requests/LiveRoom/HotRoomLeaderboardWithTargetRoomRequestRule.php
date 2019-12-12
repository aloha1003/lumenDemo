<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class HotRoomLeaderboardWithTargetRoomRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'room_id' => 'bail|required|integer',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'room_id' => __('message.room_id'),
        ];
    }
}
