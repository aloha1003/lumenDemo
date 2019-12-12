<?php
namespace App\Http\Requests\Admin\AnchorApply;

use Illuminate\Foundation\Http\FormRequest;

class AnchorApplyAgainRequestRule extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'apply_id' => 'required',
            'real_name' => 'required',
            'no' => 'required',
            'cellphone' => 'required',
            'alipay_account' => 'required',
            'photo' => 'nullable',
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
