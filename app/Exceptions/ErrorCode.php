<?php

namespace App\Exceptions;

//错误代码
class ErrorCode
{
    const OK = 200;
    const FAIL = 400;
    const BAD_REQUEST = 401; //坏请求

    const VAILD_FAIL = 100001; //验证失败
    const INPUT_FORMAT_ERROR = 100002; //输入格式错误
    const PHONE_NUMBER_OR_PASSWORD_ERROR = 100003; //手机或密码错误
    const DEVICE_IS_BLOCK = 100004; //设备被封
    const TOKEN_ERROR = 100004; //Token错误
    const MAINTAIN = 100005; //维护

    // 用戶相關錯誤 12 開頭
    const USER_NOT_FOUND = 120000;
    const USER_GOLD_NOT_ENOUGH = 120001;

    // 代理相關錯誤 18 開頭
    const AGENT_NOT_FOUND = 180000; //找不到代理
    const IS_NOT_AGENT = 180001; //不是代理
    const USER_EXIST_IN_AGENT_ROOM_LIST = 180002; //用户不在该代理房间
    const CANT_ADD_SELF_TO_ROOM_LIST = 180003; //不能新增自己进房间
    const CANT_REMOVE_SELF_FROM_ROOM_LIST = 180004; //不能移除自已出房间
    const CANT_TRANSFER_GOLD_TO_SELF = 180005; //不能转自己的资金

    const CANT_SET_SELF_STAR = 180006; //不可以设定自己星星
    const CANT_UNSET_SELF_STAR = 180007; //不可以移除自己星星
    const USER_NOT_IN_NAME_LIST = 180008; //用户不在名单中

    // 簡訊相關 19 開頭
    const SMS_VALID_CODE_EXPIRED = 190001; //简讯过期

}
