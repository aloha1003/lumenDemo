<?php
return [
    'description' => '实名认证',
    'has_verify_error' => '已经认证过了, 请勿重覆认证',
    'is_confirm_options' => [
        0 => "尚未验证",
        1 => '验证通过',
        2 => '验证失败',
        3 => '验证处理中',
        4 => '重新送审',
    ],
    'pass'=>'实名认证验证通过',
    'fail'=>'实名认证验证失败'
];
