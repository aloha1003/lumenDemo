<?php

namespace App\Http\Requests\Admin\GoldTopupApplication;

use Illuminate\Foundation\Http\FormRequest;

class InsertRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {

        return [
            'user_id' => 'required|exists:user,id',
            'user_id_repeat' => 'required|same:user_id',
            'comment' => 'required',
            'gold' => 'integer|required',
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
