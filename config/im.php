<?php

return [
    'driver' => env('IM_DRIVER', 'tencent'),
    'configure' => [
        'tencent' => [
            'sdkappid' => env('TENCENT_IM_SDK_APPID', ''),
            'identifier' => env('TENCENT_IM_IDENTIFIER', ''),
            'usersig' => env('TENCENT_IM_USERSIG', ''),
        ],
    ],
    'instances' => [
        'tencent' => [
            'injection' => 'App\Services\IM\TencentInstance',
        ],
    ],
];
