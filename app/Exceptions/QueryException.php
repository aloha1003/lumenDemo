<?php
namespace App\Exceptions;

//查询例外
class QueryException extends \Exception
{
    public $payload = [];
    public function __construct($message, $code = 0, $payload = [])
    {
        $this->message = $message;
        $this->code = $code;
        $this->payload = $payload;
    }
    public function getPayload()
    {
        return $this->payload;
        // return json_encode($this->payload, JSON_FORCE_OBJECT);
    }
    public function report()
    {
        $this->saveLogs();
        $this->sendMessages();
    }

    private function saveLogs()
    {
        \Log::warning($this->getMessage());
    }

    private function sendMessages()
    {
        event(new \App\Events\SendNotification());
    }
}
