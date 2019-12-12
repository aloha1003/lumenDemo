<?php

namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class GameBetRequestRule extends RuleRequest
{
    public function rules()
    {
        return [
             'user_id' => 'bail|required|digits:8',
             'bet_gold' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
             'game_slug' => 'bail|required|string',
             'game_round' => 'bail|required|integer',
             'order_id' => 'bail|nullable|integer',
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
            'bet_gold' => __('message.bet_gold'),
            'game_slug' => __('message.game_slug'),
            'game_round' => __('message.game_round'),
            'order_id' => __('message.order_id'),
        ];
    }
}
