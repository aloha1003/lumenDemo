<?php
namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class GameBankOffRequestRule extends RuleRequest
{
    public function rules()
    {
        return [
             'game_bank_id' => 'bail|required|integer',
             'bank_off_gold' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'game_bank_id' => __('message.game_bank_id'),
            'bank_off_gold' => __('message.bank_off_gold'),
        ];
    }
}
