<?php

namespace App\Http\Requests\Admin\SystemConfig; 
use Illuminate\Foundation\Http\FormRequest;
class SystemConfigRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {

        return [
            'value' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('systemConfig');
    }
}
