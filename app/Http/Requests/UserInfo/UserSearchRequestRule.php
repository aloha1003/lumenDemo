<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserSearchRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'search_text' => 'bail|required|string|max:40',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'search_text' => __('message.search_text'),
        ];
    }

}
