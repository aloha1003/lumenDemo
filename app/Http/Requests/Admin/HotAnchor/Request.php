<?php

namespace App\Http\Requests\Admin\HotAnchor;

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
            'user_id' => 'required|exists:user,id',
            'weight' => 'integer',
        ];
    }
    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('hotAnchor');
    }
}
