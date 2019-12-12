<?php

namespace App\Http\Requests\Admin\PayChannel;

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
            'name' => 'required',
            'fee' => 'required',
            'slug' => 'required|unique:pay_channels,slug,' . request()->route('id'),
            'available' => 'required',
            'comment' => 'nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('payChannel');
    }
}
