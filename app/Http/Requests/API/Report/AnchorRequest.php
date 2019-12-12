<?php

namespace App\Http\Requests\API\Report;

use App\Http\Requests\RuleRequest;

class AnchorRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'anchor_user_id' => 'required',
            'reason_slug' => 'required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return modelColumn('anchorReport');
    }
}
