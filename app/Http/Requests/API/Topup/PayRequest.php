<?php

namespace App\Http\Requests\API\Topup;

use App\Http\Requests\RuleRequest;

class PayRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'amount' => 'required|min:0',
            'pay_id' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('payChannelPayment');
    }
}
