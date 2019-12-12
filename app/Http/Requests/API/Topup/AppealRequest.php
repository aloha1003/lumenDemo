<?php

namespace App\Http\Requests\API\Topup;

use App\Http\Requests\RuleRequest;

class AppealRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'order_id' => 'bail|required|integer',
            'contact_info' => 'bail|required|string',
            'detail_info' => 'bail|required|string',
            'photo_url' => 'bail|nullable|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('userTopupAppeal');
    }
}
