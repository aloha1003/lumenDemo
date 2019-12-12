<?php

namespace App\Http\Requests\Admin\UserConfig;

use Illuminate\Foundation\Http\FormRequest;

class QuickRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:user,id',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('UserConfig');
    }

}
