<?php

namespace App\Http\Requests\GameAPI;

use App\Http\Requests\RuleRequest;

class CheckGameVersionRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'reg_game' => 'bail|required|string',
             'dev_id' => 'bail|required|string',
             'uuid' => 'bail|required|string',
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
