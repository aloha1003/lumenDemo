<?php
namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserStoryEditRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'story_id' => 'bail|required|integer',
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
            'story_id' => __('message.story_id'),
            'photo_url' => __('message.photo_url'),
            'title' => __('message.title'),
        ];
    }
}
