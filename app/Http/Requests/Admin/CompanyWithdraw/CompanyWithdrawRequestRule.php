<?php

namespace App\Http\Requests\Admin\CompanyWithdraw;

use Illuminate\Foundation\Http\FormRequest;

class CompanyWithdrawRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
             'payment_account' => 'bail|required|string|max:120',
             'withdraw_rmb' => 'bail|required|integer|min:1',
             //'company_comment' => 'bail|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes() 
    {
        return [
            'payment_account' => modelColumn('managerReport')['payment_account'],
            'withdraw_rmb' => modelColumn('managerReport')['withdraw_rmb'],
        ];
        
    }
}
