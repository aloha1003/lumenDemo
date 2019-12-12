<?php

use App\Exceptions\ErrorCode;

return [
    'code' => [
        // 200
        ErrorCode::OK => '請求成功',
        //400
        ErrorCode::FAIL => '請求失敗',
        ErrorCode::BAD_REQUEST => '錯誤的請求',

        //10xxxx
        ErrorCode::VAILD_FAIL => '驗證失敗',
        ErrorCode::INPUT_FORMAT_ERROR => '傳入格式錯誤',
        ErrorCode::PHONE_NUMBER_OR_PASSWORD_ERROR => '電話號碼或者密碼錯誤',
        ErrorCode::TOKEN_ERROR => 'token 驗證失敗',
        ErrorCode::MAINTAIN => '系統維護中',

        //12xxxx
        ErrorCode::USER_NOT_FOUND => '用戶不存在',
        ErrorCode::USER_GOLD_NOT_ENOUGH => '用戶餘額不足',

        // 18xxxx
        ErrorCode::AGENT_NOT_FOUND => '代理不存在',
        ErrorCode::IS_NOT_AGENT => '您不是代理',
        ErrorCode::USER_EXIST_IN_AGENT_ROOM_LIST => '用戶已在名單中',
        ErrorCode::CANT_ADD_SELF_TO_ROOM_LIST => '不能加自己到名單中',
        ErrorCode::CANT_REMOVE_SELF_FROM_ROOM_LIST => '不能從名單中移除自己',
        ErrorCode::CANT_TRANSFER_GOLD_TO_SELF => '不能轉帳給自己',
        ErrorCode::CANT_SET_SELF_STAR => '不能將自己設為我的最愛',
        ErrorCode::CANT_UNSET_SELF_STAR => '不能將自己移除我的最愛',
        ErrorCode::USER_NOT_IN_NAME_LIST => '該用戶不在名單之中',

        // 19xxxx
        ErrorCode::SMS_VALID_CODE_EXPIRED => '验证码过期，点击重新发送取得新验证码',

    ],
    'request_type_error' => '传入格式错误',
];
