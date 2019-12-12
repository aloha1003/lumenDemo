<?php

namespace App\Http\Requests\UserTopupOrder;

use App\Http\Requests\RuleRequest;

class Request extends RuleRequest
{

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
        return modelColumn('userTopupOrder');
    }
}