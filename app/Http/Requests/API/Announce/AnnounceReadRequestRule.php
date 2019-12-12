<?php

namespace App\Http\Requests\API\Announce;

use App\Http\Requests\RuleRequest;

class AnnounceReadRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'is_common' => 'bail|required|in:0,1',
            'announce_id' => 'bail|required|integer',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }
    public function attributes()
    {
        return [
            'is_common' => __('announce.is_common'),
            'announce_id' => __('announce.announce_id'),
        ];
    }
}
