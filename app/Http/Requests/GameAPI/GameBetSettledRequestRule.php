<?php

namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class GameBetSettledRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'user_id' => 'bail|required|digits:8',
             'order_id' => 'bail|required|integer',
             'status' => 'bail|required|in:1,2',
             'win_gold' => 'bail|required|integer',

        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'user_id' => __('message.user_id'),
            'order_id' => __('message.order_id'),
            'status' => __('message.status'),
            'win_gold' => __('message.win_gold'),
        ];
    }

}
