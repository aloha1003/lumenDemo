<?php

namespace App\Http\Requests\API\BlockDevice;

use App\Http\Requests\RuleRequest;

class Request extends RuleRequest
{

    public function rules()
    {
        return [
            'uuid' => 'required',
            'device_type' => 'required',
            'channel' => 'nullable',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('WithDrawGoldApply');
    }
}
