<?php

namespace App\Http\Requests\Admin\UserConfig;

use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user.currentLevel' => 'required',
            // 'user.id' => 'required',
            'userAuth.password' => 'required',
            'user.gold' => 'required',

            // 'user.nickname' => 'bail|required|unique:user,nickname',
            'real_name_verify.real_name' => 'bail|required|unique:real_name_verifies,real_name',
            'real_name_verify.no' => 'bail|required|unique:real_name_verifies,no',
            'real_name_verify.cellphone' => 'bail|required|unique:user,id',
            'real_name_verify.alipay_account' => 'bail|required|unique:real_name_verifies,alipay_account',
            'real_name_verify.photo' => 'image',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        $modelColumn = modelColumn('userConfig');
        injectLocale($modelColumn, 'user', modelColumn('User'));
        injectLocale($modelColumn, 'userAuth', modelColumn('UserAuth'));
        injectLocale($modelColumn, 'real_name_verify', modelColumn('realNameVerify'));
        return $modelColumn;
    }
}
