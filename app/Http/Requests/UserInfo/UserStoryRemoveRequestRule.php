<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserStoryRemoveRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'story_id' => 'bail|required|integer'
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'story_id' => __('message.story_id')
        ];
    }
}
