<?php
return [
    'description' => '主播举报',
    'report_status_options' => [
        0 => '尚未处理',
        1 => '已通过举报，并且封锁主播',
        2 => '驳回，不封主播',
    ],
    'report_error_operation' => '已经执行过审核，请勿重覆执行',
    'report_error_operation_creating' => '错误的审核状态',
];
