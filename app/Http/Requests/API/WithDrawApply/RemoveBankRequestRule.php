<?php

namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class RemoveBankRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'bank_info_id' => 'required'
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
