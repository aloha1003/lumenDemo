<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class BatchQueryRequest extends RuleRequest
{

    public function rules()
    {
        return [
            'query_value' => 'string|required_if:payment_type,game',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            'query_value' => __('games.game_slug'),
        ];
    }
}
