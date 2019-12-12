<?php

namespace App\Http\Requests\Admin\DestoryGold;

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
            'user_id_confirm' => 'required|same:user_id',
            'reason' => 'required',
            'gold' => 'integer|required|min:1',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('destoryGold');
    }
}
