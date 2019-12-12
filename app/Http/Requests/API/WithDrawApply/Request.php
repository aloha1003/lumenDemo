<?php

namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class Request extends RuleRequest
{

    public function rules()
    {
        return [
            'rmb' => 'required|integer|min:100|max:100000',
            'account' => 'required',
            'account_confirm' => 'required|same:account',
            'payment_channels_slug' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('WithDrawGoldApply');
    }
}
