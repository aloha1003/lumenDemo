<?php

namespace App\Http\Requests\API\RollAd;

use App\Http\Requests\RuleRequest;

class IndexRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'platform' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            'platform' => modelColumn('rollAd')['platform'],
        ];
    }
}
