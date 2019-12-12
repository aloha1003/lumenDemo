<?php

namespace App\Http\Requests\UserInfo;

use App\Http\Requests\RuleRequest;

class UserSetFeedbackRequestRule extends RuleRequest
{

    public function rules()
    {
        $typeSlugList = sc('feedbackType');
        $typeSlugKey = array_keys($typeSlugList);
        return [
            'type_slug' => 'bail|required|string|in:' . implode(',', $typeSlugKey),
            'contact_info' => 'bail|required|no_html',
            'feedback_info' => 'bail|required|no_html',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'type_slug' => __('message.feedback_type'),
            'contact_info' => __('message.contact_info'),
            'feedback_info' => __('message.feedback_info'),
        ];
    }

    public function filters()
    {
        return [
            'feedback_info' => 'escape',
            'contact_info' => 'escape',
        ];
    }

}
