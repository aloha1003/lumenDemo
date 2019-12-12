<?php
namespace App\Http\Requests\Register;

use App\Http\Requests\RuleRequest;

class RegisterRequestRule extends RuleRequest
{

    public function rules()
    {
        return [
            'cellphone' => 'bail|required|cellphone|unique:user,cellphone',
            'password' => 'bail|required|size:32',
            'sms_code' => 'bail|required|digits:6',

            'channel' => 'bail|required|string',
            'uuid' => 'bail|required|string',
            'device_type' => 'bail|required|string',
            'os_version' => 'bail|required|in:android,ios',
        ];
    }

    public function messages()
    {
        return __('requestMessage');
    }

    public function attributes()
    {
        return [
            'cellphone' => __('message.cellphone'),
            'password' => __('message.password'),
            'sms_code' => __('message.sms_code'),

            'channel' => __('message.channel'),
            'uuid' => __('message.device_uuid'),
            'device_type' => __('message.device_type'),
            'os_version' => __('message.os_version'),
        ];
    }
}
