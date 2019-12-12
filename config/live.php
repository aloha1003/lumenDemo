<?php

return [
    'default' => env('LIVE_DRIVER', 'tencent'),

    'instances' => [
        'tencent' => [
            'injection' => 'App\Services\Live\TencentInstance',
            'config' => 'tencent-live', //目前改抓系统参数的 tencent
        ],
        'netease' => [
            'injection' => 'App\Services\Live\NeteaseInstance',
            'config' => 'netease-live',
        ],
    ],
];
