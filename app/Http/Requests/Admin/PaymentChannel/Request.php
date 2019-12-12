<?php

namespace App\Http\Requests\Admin\PaymentChannel;

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
            'title' => 'required',
            'slug' => 'required',
            'fee_type' => 'required|unique:payment_channels,slug,' . request()->route('id'),
            'fee' => 'required',
            'step_fee' => 'array|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('paymentChannel');
    }
}
