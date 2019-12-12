<?php

namespace App\Services\Sms;

use Carbon\Carbon;

class BasicInstance
{
    protected $config;
    protected $path;
    protected $random;
    protected $time;
    protected $messageType;

    public function __construct(string $config)
    {
        $this->config = config($config);
        $this->path = $this->config['api_path'] ?? '';
        $this->setting();
    }

    protected function random(): int
    {
        return rand(100000, 999999);
    }

    protected function setting()
    {
        $this->random = $this->random();
        $this->time = Carbon::now()->timestamp;
    }
    /**
     * 设置 发送消息 类型
     *
     *
     * @param    string                   $messageType [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-28T08:59:06+0800
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
        return $this;
    }

    public function send(array $parameters)
    {
        \Queue::pushOn(pool('sms'), new \App\Jobs\SendSMSJob($this->messageType, $parameters));
    }
}
