<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserSetStoryMainPhotoUrlRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'url' => 'bail|required|string|max:1000',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'url' => __('message.photo_url'),
        ];
    }
}
