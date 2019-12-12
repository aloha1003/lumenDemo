<?php

namespace App\Http\Requests\Barrage;

use App\Http\Requests\RuleRequest;

class BarragePurchaseRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'room_id' => 'bail|required|numeric',
             'barrage_id' => 'bail|required|numeric',
             'message' => 'bail|required|string',
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
            'barrage_id' => __('message.barrage_id'),
            'message' => __('message.message'),
        ];
    }
}
