<?php

namespace App\Http\Requests\Admin\BaseHotConfigure;

use App\Http\Requests\RuleRequest;

class BaseHotConfigureRequest extends RuleRequest
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
        return modelColumn('baseHotConfigure');
    }
}