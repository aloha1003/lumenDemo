<?php

namespace App\Services\Payments\Instances\Didipay;

use App\Services\Payments\ConfigInterface;
use App\Services\Payments\Instances\Didipay\DidipayCommon;
use App\Services\Payments\PaymentInterface;

class WEIXINPayment extends DidipayCommon implements PaymentInterface, ConfigInterface
{
    protected $notifyColumnMap = [];
    //返回方式 qrcode 或是 link
    protected $callBackType = 'link';
    protected $payWayCode = self::PAY_WAY_CODE_WECHAT_SCAN;

}
