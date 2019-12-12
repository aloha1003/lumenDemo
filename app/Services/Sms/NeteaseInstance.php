<?php

namespace App\Services\Sms;

use App\Services\CurlApiService;
use Carbon\Carbon;

/**
 * 參考: https://github.com/qcloudsms/qcloudsms_php
 */
class NeteaseInstance extends BasicInstance implements SmsInterface
{
    private $config;
    private $service;
    private $random;
    private $time;

    public function __construct(string $config)
    {
        $this->config = config($config);
        $this->path = $this->config['api_path'];
        $this->setting = $this->setting();
        $this->service = new CurlApiService($config);
        $this->prepare();
    }

    public function random(): int
    {
        return rand(100000, 999999);
    }

    public function setting()
    {

    }

    public function prepare()
    {
        $this->random = $this->random();
        $this->time = Carbon::now()->timestamp;
        // $secret = config('app.neteasy.SECRET');
        // [
        //     'AppKey' => config('app.neteasy.KEY'),
        //     'Nonce' => $nonce,
        //     'CurTime' => $curTime,
        //     'CheckSum' => $checkSum,
        //     'Content-Type' => "application/json;charset=utf-8",
        // ];
    }

    public function get(string $key)
    {
        dd('');
    }

    public function send(array $parameters)
    {
        dd('NeteaseInstance');
    }

    private function generatePath(string $path, string $query)
    {
        return $this->path['send'] . '?' . $query;
    }
    
    private function generateQuery(array $parameters)
    {
        return http_build_query($parameters);
    }

    public function after(string $path, array $parameters)
    {
        $response = $this->service->setPath($this->path['send'] . '?' . $query)->post($data);
        if ($this->service->isSuccessed()) {

        }
    }
}
