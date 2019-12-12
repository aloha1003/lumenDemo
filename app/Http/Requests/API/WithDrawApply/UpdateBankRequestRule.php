<?php

namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class UpdateBankRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'bank_info_id' => 'required',
            'name' => 'required',
            'account' => 'required',
            'other_info' => 'string|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('bankInfo');
    }
}
