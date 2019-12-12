<?php

namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class WithdrawAppealRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'withdraw_id' => 'bail|required|integer',
            'contact_info' => 'bail|required|string',
            'detail_info' => 'bail|required|string',
            'photo_url' => 'bail|nullable|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('withdrawAppeal');
    }
}
