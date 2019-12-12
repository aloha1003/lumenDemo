<?php

return [
    'default' => env('SMS_DRIVER', 'qiniu'),
    'real_send' => env('SMS_REAL_SEND', 1),
    'instances' => [
        'tencent' => [
            'injection' => 'App\Services\Sms\TencentInstance',
            'config' => 'tencent-sms',
        ],
        'netease' => [
            'injection' => 'App\Services\Sms\NeteaseInstance',
            'config' => 'netease-sms',
        ],
        'qiniu' => [
            'injection' => 'App\Services\Sms\QiniuInstance',
            'config' => 'qiniu-sms',
        ],
    ],
];
