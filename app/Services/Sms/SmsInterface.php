<?php

namespace App\Services\Sms;

interface SmsInterface
{
    //发送讯息类型
    const MESSAGE_TYPE_REGISTER_VERIFY = 100; //验证码
    const MESSAGE_TYPE_RESENT_PASSWORD = 200; //重设密码
    public function send(array $parameters);
    /**
     * 设定讯息类型
     *
     * @param    [type]                   $messageType [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T09:59:19+0800
     */
    public function setMessageType($messageType);
    /**
     * 透过消息类型 返回 讯息
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T10:00:26+0800
     */
    public function getMessageFromMessageType();

}
