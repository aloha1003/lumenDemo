<?php

namespace App\Services\Payments\Instances\JPAY;

use App\Services\Payments\ConfigInterface;
use App\Services\Payments\Instances\JPAY\JPAYCommon;
use App\Services\Payments\PaymentInterface;

class WEIXINPayment extends JPAYCommon implements PaymentInterface, ConfigInterface
{
    protected $notifyColumnMap = [];
    protected $callBackType = 'link';
    protected $payType = 3; //微信
}
