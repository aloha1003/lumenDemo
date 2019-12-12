<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class GiftStatisticsRequestRule extends RuleRequest
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
            'room_id' => __('message.room_id'),
        ];
    }
}
