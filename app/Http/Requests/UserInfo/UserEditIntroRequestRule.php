<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserEditIntroRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'intro' => 'bail|nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'intro' => __('message.intro'),
        ];
    }
}
