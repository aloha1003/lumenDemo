<?php

namespace App\Services;

//系统服务
class SystemService
{

    const CONFIG_COIN_RATIO = 'coinRatio';
    const CONFIG_WITHDRAW_FRONT_SHOW = 'withDrawFrontShow';
    const USER_LOGIN_SALT = 'auth_login_salt';
    const CONFIG_ANCHOR_REPORT_REASON = 'anchorReportReason';
    const CONFIG_FEEDBACK_TYPE = 'feedbackType';
    const CONFIG_SUPPORT_PAY_TYPE = 'support_pay_type';

    public function __construct()
    {

    }

    /**
     * 取得前端顯示的系統設定
     *
     * @return array
     */
    public function getConfigForFront()
    {
        $coinRatioConfig = sc(self::CONFIG_COIN_RATIO);
        $withdrawFrontShowConfig = sc(self::CONFIG_WITHDRAW_FRONT_SHOW);
        $userLoginSalt = sc(self::USER_LOGIN_SALT);

        $feedbackConfig = sc(self::CONFIG_FEEDBACK_TYPE);
        $feedbackConfigArray = [];
        foreach ($feedbackConfig as $key => $data) {
            $feedbackConfigArray[] = [
                'key' => $key,
                'value' => $data,
            ];
        }

        $anchorReportReasonConfig = sc(self::CONFIG_ANCHOR_REPORT_REASON);
        $anchorReportReasonConfigArray = [];
        foreach ($anchorReportReasonConfig as $key => $data) {
            $anchorReportReasonConfigArray[] = [
                'key' => $key,
                'value' => $data,
            ];
        }

        $supportPayTypeConfig = sc(self::CONFIG_SUPPORT_PAY_TYPE);
        $supportPayTypeConfigArray = [];
        foreach ($supportPayTypeConfig as $key => $data) {
            $supportPayTypeConfigArray[] = [
                'key' => $key,
                'value' => $data,
            ];
        }

        $result = [
            self::CONFIG_COIN_RATIO => $coinRatioConfig,
            self::CONFIG_WITHDRAW_FRONT_SHOW => $withdrawFrontShowConfig,
            self::USER_LOGIN_SALT => $userLoginSalt,
            self::CONFIG_ANCHOR_REPORT_REASON => json_encode($anchorReportReasonConfigArray, JSON_UNESCAPED_UNICODE),
            self::CONFIG_FEEDBACK_TYPE => json_encode($feedbackConfigArray, JSON_UNESCAPED_UNICODE),
            self::CONFIG_SUPPORT_PAY_TYPE => json_encode($supportPayTypeConfigArray, JSON_UNESCAPED_UNICODE),
        ];
        return $result;
    }
}
