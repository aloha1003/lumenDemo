<?php

namespace App\Http\Requests\Admin\UserConfig;

use Illuminate\Foundation\Http\FormRequest;

class SaveRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'user.currentLevel' => 'required',
            'userAuth.password' => '',
            'user.gold' => 'required',
            // 'user.nickname' => 'bail|required|unique:user,nickname',
            'real_name_verify.real_name' => 'bail|required|unique:user,nickname,' . request()->get('user_id'),
            'real_name_verify.no' => 'bail|required|unique:real_name_verifies,' . request()->get('user_id') . ',user_id',
            'real_name_verify.cellphone' => 'bail|required|unique:user,cellphone,' . request()->get('user_id'),
            'real_name_verify.alipay_account' => 'bail|required|unique:real_name_verifies,' . request()->get('user_id') . ',user_id',
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
