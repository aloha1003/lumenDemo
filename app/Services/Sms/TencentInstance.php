<?php

namespace App\Services\Sms;

use App\Services\CurlApiService;

/**
 * 參考: https://github.com/qcloudsms/qcloudsms_php
 */
class TencentInstance extends BasicInstance implements SmsInterface
{
    protected $config;
    protected $random;
    protected $time;
    private $service;

    public function __construct(string $config)
    {
        parent::__construct($config);
        $this->service = new CurlApiService($config);
    }

    public function send(array $parameters)
    {
        $data = $this->generateData($parameters);
        $path = $this->generatePath($this->path['send']);

        $response = $this->service->setPath($path)->post($data);
        if (!$this->service->isSuccessed()) {
            \Log::warning(json_encode($response));
        }
        return $response;
    }

    /**
     * 產生送出使用之data
     *
     * @param array $parameters
     * @return array
     */
    protected function generateData(array $parameters): array
    {
        return [
            'tel' => [
                'nationcode' => $parameters['nationcode'],
                'mobile' => $parameters['mobile'],
            ],
            'type' => (int) \App\Models\SmsCode::SEND_TYPE_ODRINARY,
            'msg' => $parameters['message'],
            'sig' => $this->encrypt($parameters),
            'time' => $this->time,
            'extend' => $parameters['extend'] ?? '',
            'ext' => $parameters['ext'] ?? '',
        ];
    }

    /**
     * 產生送出用路徑
     *
     * @param string $path
     * @return string
     */
    protected function generatePath(string $path): string
    {
        return $this->path['send'] . '?' . http_build_query([
            'sdkappid' => $this->config['app']['id'],
            'random' => $this->random,
        ]);
    }

    /**
     * 產生加密資訊
     *
     * @param array $parameters
     * @return string
     */
    protected function encrypt(array $parameters): string
    {
        return hash('sha256', http_build_query([
            'appkey' => $this->config['app']['key'],
            'random' => $this->random,
            'time' => $this->time,
            'mobile' => $parameters['mobile'],
        ]), false);
    }

    public function getMessageFromMessageType()
    {

    }
}
