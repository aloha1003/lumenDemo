<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\RuleRequest;

class AgentTransferGoldRequestRule extends RuleRequest
{

    public function rules()
    {

        return [
            'target_user_id' => 'bail|required|numeric',
            'gold' => 'bail|required|regex:/^\d*(\.\d{1,2})?$/',
            'comment' => 'string|nullable|size:256',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'target_user_id' => __('agentTransaction.target_user_id'),
            'gold' => __('agentTransaction.gold'),
        ];
    }
}
