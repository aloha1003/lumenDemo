<?php
namespace App\Http\Requests\Agent;

use App\Http\Requests\RuleRequest;

class AgentSetUserToStarRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'target_user_id' => 'bail|required|numeric',
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
        ];
    }
}
