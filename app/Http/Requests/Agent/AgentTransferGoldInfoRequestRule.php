<?php

namespace App\Http\Requests\Agent;

use App\Http\Requests\RuleRequest;

class AgentTransferGoldInfoRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'start_date' => 'nullable|date',
             'end_date' => 'nullable|date',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'start_date' => __('agentTransaction.start_date'),
            'end_date' => __('agentTransaction.end_date'),
        ];
    }
}