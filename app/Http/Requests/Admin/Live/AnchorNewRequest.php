<?php

namespace App\Http\Requests\Admin\Live; 
use Illuminate\Foundation\Http\FormRequest;
class AnchorNewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'level' => 'required',
            'password' => 'required_if:id,',
            'gold' => 'required',
            'manager' => 'exists:managers,id',
            'real_name' => 'required',
            'no' => 'required',
            'cellphone' => 'required',
            'alipay_account' => 'required',
            'photo' => 'image|required_if:id,',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('anchorInfo');
    }
}
