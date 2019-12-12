<?php

namespace App\Services\Payments\Instances\JPAY;

use App\Services\Payments\ConfigInterface;
use App\Services\Payments\Instances\JPAY\JPAYCommon;
use App\Services\Payments\PaymentInterface;

class ALIPayment extends JPAYCommon implements PaymentInterface, ConfigInterface
{
    protected $notifyColumnMap = [];
    protected $callBackType = self::CALLBACK_TYPE_LINK;
    protected $payType = 1; //支付宝

}
