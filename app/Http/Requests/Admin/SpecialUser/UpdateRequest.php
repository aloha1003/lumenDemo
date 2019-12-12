<?php

namespace App\Http\Requests\Admin\SpecialUser;

use App\Http\Requests\RuleRequest;

class UpdateRequest extends RuleRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            'user_type' => 'required',
            'name' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('specialUser');
    }
}
