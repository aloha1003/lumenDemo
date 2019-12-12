<?php

namespace App\Http\Requests\Admin\GoldTopupApplication;

use Illuminate\Foundation\Http\FormRequest;

class ReviewRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'status' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('goldTopupApplication');
    }
}
