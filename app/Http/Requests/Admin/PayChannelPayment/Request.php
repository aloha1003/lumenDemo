<?php

namespace App\Http\Requests\Admin\PayChannelPayment;

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
            'pay_channels_slug' => 'required',
            'fee' => 'required|unique:pay_channels,slug,' . request()->route('id'),
            'alias' => 'required',
            'pay_type' => 'required',
            'available' => 'required',
            'order_amounts' => 'required',
            'custom_amount' => 'required',
            'high_quality' => 'required',
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
