<?php

namespace App\Http\Requests\Admin\UserConfig;

use Illuminate\Foundation\Http\FormRequest;

class BlockRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'block_reason' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('userConfig');
    }
}
