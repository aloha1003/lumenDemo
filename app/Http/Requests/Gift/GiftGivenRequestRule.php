<?php

namespace App\Http\Requests\Gift;

use App\Http\Requests\RuleRequest;

class GiftGivenRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'room_id' => 'bail|required|numeric',
             'gift_id' => 'bail|required',
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
            'gift_id' => __('message.gift_id'),
        ];
    }
}
