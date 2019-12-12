<?php

namespace App\Http\Requests\API\AppVersion;

use App\Http\Requests\RuleRequest;

class AppVersionRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'channel_slug' => 'bail|required|string',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            'channel_slug' => __('channel.channel_slug'),
        ];
    }
}
