<?php

namespace App\Http\Requests\API\Topup;

use App\Http\Requests\RuleRequest;

class QueryRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'page' => 'integer|nullable',
            'filter_data' => 'json|nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('payChannelPayment');
    }
}
