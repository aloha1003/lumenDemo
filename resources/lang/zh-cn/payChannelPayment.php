<?php
return [
    'main_title' => '支付方式',
    'description' => '支付方式',
    'custom_amount_list' => [
        0 => '否',
        1 => '是',
    ],
    'high_quality_list' => [
        0 => '否',
        1 => '是',
    ],
    'error' => [
        'is_not_available' => '当前支付尚未启用, 麻烦请改用其他支付方式，谢谢',
        'not_valid_amount' => '无效的支付金额, 麻烦重新输入，谢谢',
        'not_valid_api_response' => '第三方支付发生未预期的错误, :response',
        'not_valid_api_json_response' => '第三方支付发生未预期的错误, :response',
        'not_valid_api_notify_request' => '第三方支付，回调请求资料格式错误, :request',
        'valid_api_error' => '第三方支付出现错误, :response',
        'signature_error' => '签名错误',
        'not_set_transaction_no' => '找不到交易编号，无法更新DB',
        'trasaction_not_finish' => '交易尚未成功',
    ],
    'common_error' => '支付失败，错误编号 :no , 麻烦反馈客服',
    'not_valid_payment' => '找不到该笔有效的交易方式',
];
