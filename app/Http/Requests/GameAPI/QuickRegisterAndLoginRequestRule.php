<?php

namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class QuickRegisterAndLoginRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'resversion' => 'bail|required|string',
             'channel' => 'bail|required|string',
             'reg_game' => 'bail|required|string',
             'osver' => 'bail|required|string',
             'appver' => 'bail|required|string',
             'line_no' => 'bail|required|string',
             'sim_serial' => 'bail|required|string',
             'dev_id' => 'bail|required|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
        ];
    }
}
