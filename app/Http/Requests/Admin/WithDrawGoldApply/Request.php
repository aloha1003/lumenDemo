<?php

namespace App\Http\Requests\Admin\WithDrawGoldApply;

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
            'status' => 'required',
            'comment' => 'nullable',
            'from_source_user_id' => 'nullable',
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
