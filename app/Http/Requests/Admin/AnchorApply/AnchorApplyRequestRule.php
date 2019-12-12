<?php
namespace App\Http\Requests\Admin\AnchorApply;

use Illuminate\Foundation\Http\FormRequest;

class AnchorApplyRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'real_name' => 'required',
            'no' => 'required',
            'cellphone' => 'required',
            'alipay_account' => 'required',
            'photo' => 'required',
            'password' => 'required',
            'manager_id' => 'required'
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('companyAnchorApply');
    }
}
