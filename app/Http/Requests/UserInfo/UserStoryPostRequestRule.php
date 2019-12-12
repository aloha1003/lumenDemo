<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserStoryPostRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'photo_url' => 'bail|required|string|max:1024',
            'title' => 'bail|required|string|max:500',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'photo_url' => __('message.photo_url'),
            'title' => __('message.title'),
        ];
    }
}
