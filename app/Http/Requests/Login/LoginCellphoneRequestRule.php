<?php

namespace App\Http\Requests\Login;

use App\Http\Requests\RuleRequest;

// use Illuminate\Http\Request as RuleRequest;

class LoginCellphoneRequestRule extends RuleRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [];
        return [
            'cellphone' => 'bail|required|digits:11',
            'password' => 'bail|required',
            //'device_id' => 'bail|required',
            'os_version' => 'bail|required|in:android,ios',
        ];
    }

    public function messages()
    {   
        
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'cellphone' => __('message.cellphone'),
            'password' => __('message.password'),
            //'device_id' => __('message.device_id'),
            'os_version' => __('message.os_version'),
        ];
    }
}
