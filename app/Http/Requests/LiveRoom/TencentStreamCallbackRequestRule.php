<?php

namespace App\Http\Requests\LiveRoom;

use App\Http\Requests\RuleRequest;

class TencentStreamCallbackRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
             'appid' => 'integer|required',

             'app' => 'string|required',
             'appname' => 'string|required',
             'stream_id' => 'string|required',
             'channel_id' => 'string|required',
             'event_time' => 'integer|required',
             'sequence' => 'string|required',
             'node' => 'string|required',
             'user_ip' => 'string|required',
             'stream_param' => 'string|required',
             'push_duration' => 'string|required',
             'errcode' => 'string|required',
             'errmsg' => 'string|required',

             'event_type' => 'integer|required',
             'sign' => 'string|required',
             't' => 'integer|required',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            //'query_value' => __('games.game_slug'),
            //'query_type' => __('liveRoom.query_type'),
        ];
    }
}