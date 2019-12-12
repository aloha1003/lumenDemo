<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class QueryRequest extends RuleRequest
{

    public function rules()
    {
        return [
             'query_value' => 'string|required_if:payment_type,game',
             'query_type' => 'string|required',
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
            'query_type' => __('liveRoom.query_type'),
        ];
    }
}