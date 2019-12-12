<?php

namespace App\Http\Requests\Admin\SpecialAccount;

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
            'user_id_repeat' => 'required|same:user_id',
            'account' => 'required',
            'gold' => 'integer|required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('specialAccount');
    }
}
