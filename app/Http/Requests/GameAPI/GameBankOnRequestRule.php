<?php

namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class GameBankOnRequestRule extends RuleRequest
{
    public function rules()
    {
        return [
             'user_id' => 'bail|required',
             'game_slug' => 'bail|required|string',
             'bank_on_gold' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
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
            'game_slug' => __('message.game_slug'),
            'bank_on_gold' => __('message.bank_on_gold'),
        ];
    }
}
