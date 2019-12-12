<?php
return [
    'description' => '用户充值记录',
    'main_title' => '用户充值记录',
    'filter_data' => '搜寻条件',
    'error_handler_illegal_pay_step' => '取消当前操作，不符合正常的处理阶段',
    'amount_mismatch' => '金额不符',
    'has_finish_this_order' => '已经上分过了，请勿重复提交',
    'order_not_yet_finish' => '该笔订单，尚未完成支付',
    'order_already_set_appeal' => '订单已提出申诉',
    'order_not_exist' => '订单不存在',
    'not_your_order' => '不是你的订单',

    'pay_step_list' => [
        'INIT' => '订单已建立',
        'THIRD_ERR' => '第三方支付回应失败',
        'THIRD_CALLBACK_ERR' => '第三方支付回调失败',
        'PEND' => '等待玩家付款',
        'SUCCESS' => '交易成功',
        'ABORT' => '交易逾时失败',
        'CANCEL' => '无效订单',
    ],
    'pay_step_color_list' => [
        'INIT' => 'black',
        'THIRD_ERR' => 'red',
        'THIRD_CALLBACK_ERR' => 'red',
        'PEND' => 'black',
        'SUCCESS' => 'green',
        'ABORT' => '#eba434',
        'CANCEL' => 'red',
    ],
    'pay_step_list_front' => [
        'INIT' => '处理中',
        'THIRD_ERR' => '处理中',
        'THIRD_CALLBACK_ERR' => '处理中',
        'PEND' => '处理中',
        'SUCCESS' => '已上分',
        'ABORT' => '处理中',
        'CANCEL' => '无效订单',
    ],
    'notify_status_list' => [
        0 => '尚未异步',
        1 => '异步成功',
        2 => '异步失败',
    ],

];
