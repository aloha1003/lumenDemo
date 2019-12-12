<?php

namespace App\Services\Payments;

class BasePaymentCommon
{
    protected $config;
    public function __construct()
    {
        app()->configure('repository');
        $this->setConfig();
    }

    /**
     * 取得设定档
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:05:20+0800
     */
    public function config()
    {
        return $this->config;
    }
}
