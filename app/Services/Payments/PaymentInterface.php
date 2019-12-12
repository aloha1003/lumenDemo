<?php

namespace App\Services\Payments;

interface PaymentInterface
{
    /**
     * 取得付款资讯
     *
     * @param    array                    $request [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:07:25+0800
     */
    public function pay(array $request);
}
