<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $messageType; //发送何种简讯类型
    public $parameters; //发送简讯所需的参数
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($messageType, $parameters)
    {
        $this->parameters = $parameters;
        $this->messageType = $messageType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //真正发送简讯
        \Sms::setMessageType($this->messageType)->doSendJob($this->parameters);
    }
}
