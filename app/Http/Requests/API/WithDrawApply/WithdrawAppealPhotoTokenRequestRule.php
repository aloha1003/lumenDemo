<?php
namespace App\Http\Requests\API\WithDrawApply;

use App\Http\Requests\RuleRequest;

class WithdrawAppealPhotoTokenRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'withdraw_id' => 'bail|required|integer',
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
