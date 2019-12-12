<?php

namespace App\Http\Requests\Admin\User; 
use Illuminate\Foundation\Http\FormRequest;
class RealNameVerifyRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'is_confirm' => 'required',
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