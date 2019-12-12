<?php

namespace App\Http\Requests\Admin\UserTopupReport;

use App\Http\Requests\RuleRequest;

class Request extends RuleRequest
{
    public function authorize()
    {
        return true;
    }
    public function rules()
    {
        return [
            
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('userTopupReport');
    }
}