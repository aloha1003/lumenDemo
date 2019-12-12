<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class LeaderbordInLiveRoomRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'room_id' => 'bail|required|numeric',
            'anchor_id' => 'bail|required|digits:8',
            'number' => 'bail|required|integer|min:5|max:50'
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
            'anchor_id' => __('message.anchor_id'),
            'number' => __('message.number'),
        ];
    }

}
