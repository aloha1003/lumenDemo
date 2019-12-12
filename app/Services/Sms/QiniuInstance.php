<?php

namespace App\Services\Sms;

use App\Exceptions\QueryException;
use Qiniu\Auth;
use Qiniu\Http\Error;
use Qiniu\Sms\Sms;

/**
 * 參考: https://github.com/qcloudsms/qcloudsms_php
 */
class QiniuInstance extends BasicInstance implements SmsInterface
{
    protected $config;
    protected $random;
    protected $time;

    public function __construct(string $config)
    {
        parent::__construct($config);
    }

    /**
     * 透过讯息类型返回讯息
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T10:08:58+0800
     */
    public function getMessageFromMessageType()
    {
        $sc = sc('qiniu');
        switch ($this->messageType) {
            case self::MESSAGE_TYPE_REGISTER_VERIFY:
                return $sc['QINIU_SMS_TEMPLATE_ID'];
                break;
            case self::MESSAGE_TYPE_RESENT_PASSWORD:
                return $sc['QINIU_SMS_TEMPLATE_FORGET_TEMPLATE_ID'];
                break;

            default:
                return $sc['QINIU_SMS_TEMPLATE_ID'];
                break;
        }
    }
    /**
     * 发送消息
     *
     * @param    array                    $parameters [description]
     *
     * @return   [type]                               [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T10:15:32+0800
     */
    public function doSendJob(array $parameters)
    {

        $sc = sc('qiniu');
        $accessKey = $sc['QINIU_ACCESS_KEY'];
        $secretKey = $sc['QINIU_SECRET_KEY'];
        $templateId = $this->getMessageFromMessageType();
        $auth = new Auth($accessKey, $secretKey);
        $client = new Sms($auth);
        //发送信息模块
        $code = $this->getMessageDataByMessageType($parameters);

        try {
            //发送短信
            $resp = $client->sendMessage($templateId, [$parameters['mobile']], $code);
            $this->verifyAPIResult($resp);
        } catch (\Exception $e) {
            wl($e);
            throw $e;
        }
    }

    /**
     * 透过讯息类型返回讯息
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T10:08:58+0800
     */
    private function getMessageDataByMessageType($parameters)
    {
        switch ($this->messageType) {
            case self::MESSAGE_TYPE_REGISTER_VERIFY:
                return array('code' => $parameters['message']);
                break;
            case self::MESSAGE_TYPE_RESENT_PASSWORD:
                return array('code' => $parameters['message']);
                break;

            default:
                return [];
                break;
        }
    }

    private function verifyAPIResult($result)
    {
        //长度必为二
        if (count($result) != 2) {
            throw new QueryException(__('sms.sms_error_format_error'), 0, $result);
        }
        // 1是 错误
        if (is_a($result[1], Error::class)) {
            throw new QueryException(__('sms.sms_error_reponse_failure'), 0, $result[1]->getResponse());
        }
    }

}
