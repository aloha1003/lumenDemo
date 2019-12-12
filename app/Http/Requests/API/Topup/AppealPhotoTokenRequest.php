<?php

namespace App\Http\Requests\API\Topup;

use App\Http\Requests\RuleRequest;

class AppealPhotoTokenRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'order_id' => 'bail|required|integer',
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
