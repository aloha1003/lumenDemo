<?php

namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class AddBankRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'payment_channels_slug' => 'required',
            'account' => 'required',
            'bank_slug' => 'string|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('BankInfo');
    }
}
