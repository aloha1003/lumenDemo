<?php

namespace App\Http\Requests\Admin\Live;

use Illuminate\Foundation\Http\FormRequest;

class ManagerRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'name' => 'required',
            'password' => 'required_if:id,',
            'status' => 'required',
            'role' => 'required',
            'id' => '',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('manager');
    }
}
