<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class RealNameApplyRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'real_name' => 'bail|required|min:2|alpha',
            'no' => 'bail|required|min:18|alpha_num|unique:real_name_verifies,no,' . id() . ',user_id',
            'cellphone' => 'bail|required|cellphone|unique:real_name_verifies,cellphone,' . id() . ',user_id',
            'alipay_account' => 'bail|required|alipay_account',
            'photo' => 'bail|nullable|image',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return modelColumn('RealNameVerify');
    }

}
