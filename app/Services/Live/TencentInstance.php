<?php

namespace App\Services\Live;

use App\Services\CurlApiService;
use TencentCloud\Common\Credential;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Live\V20180801\LiveClient;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamEventListRequest;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamOnlineListRequest;
use TencentCloud\Live\V20180801\Models\DescribeLiveStreamStateRequest;
use TencentCloud\Live\V20180801\Models\DropLiveStreamRequest;
use Tencent\TLSSigAPI;

/**
 * 腾讯直播实例
 */
class TencentInstance implements LiveInterface
{
    private $config;
    private $service;
    const SIGN_CACHE_PRE_FIX = 'tencent_sign:';
    const INVALID_STRING = 'inactive';
    // public function __construct(string $config)
    // {
    //     $config = getTencentLiveConfigFromCache();
    //     config($config);
    //     $this->config = $config;
    //     $this->path = $this->config['api_path'];
    //     $this->system = $this->config['system'];
    //     $this->setting = $this->setting();
    //     $this->service = new CurlApiService($config);
    // }

    public function initWithConfig()
    {
        $config = getTencentLiveConfigFromCache();
        config($config);
        $this->config = $config;
        $this->path = $this->config['api_path'];
        $this->system = $this->config['system'];
        $this->setting = $this->setting();
        $this->service = new CurlApiService($config);
    }

    public function setting()
    {

    }

    public function sign($generateName)
    {
        $this->initWithConfig();
        $tLSSigAPI = app(TLSSigAPI::class);
        $tLSSigAPI->setAppid($this->system['play_app_id']);
        $tLSSigAPI->setPrivateKey($this->system['private_key']);
        $sig = $tLSSigAPI->genSig($generateName);
        return $sig;
        // $api->SetAppid(140000000);
        // $private = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'private_key');
        // $api->SetPrivateKey($private);
        // $sig = $api->genSig('xiaojun');
    }
    public function get(string $key)
    {
        dd('');
    }

    public function create()
    {

    }

    /**
     * 產生數位簽章
     *
     * @param string $generateName
     * @return string
     */
    public function userSig(string $generateName = '', $force = false): string
    {
        $cacheKey = $this->getSignLockPrefix() . $generateName;
        $lockKey = $cacheKey . 'lock';
        $sign = \Cache::get($cacheKey);
        if ($sign) {
            return $sign;
        } else {
            if ($force) {
                releaseRedisLock($lockKey);
                return $this->doCreateSign($generateName);
            } else {
                $isLock = redisLock($lockKey);
                if (!$isLock) {
                    return '';
                }
                \Queue::pushOn(pool('sign'), new \App\Jobs\ImSignGenerate($generateName));
                return '';
            }
        }
    }

    /**
     * 真正產生數位簽名
     *
     * @param    string                   $generateName [description]
     *
     * @return   [type]                                 [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-08T16:49:14+0800
     */
    public function doCreateSign(string $generateName = ''): string
    {
        $cacheKey = $this->getSignLockPrefix() . $generateName;
        $sign = \Cache::get($cacheKey);
        if ($sign) {
            return $sign;
        }
        $this->initWithConfig();
        $service = app(\App\Services\TencentService::class);
        $service->setAppid($this->system['play_app_id']);
        $service->setPrivateKey($this->system['private_key']);
        $sig = $service->genSig($generateName);
        \Cache::put($cacheKey, $sig, 86400);
        return $sig;
    }

    /**
     * 產生推拉流url
     *
     * @param string $streamName
     * @param string $time
     * @return void
     */
    public function pushPullFlow(string $streamName, string $time)
    {
        $this->initWithConfig();
        $domain = $this->system['domain'];
        $playDomain = $this->system['play_domain'];
        $appName = $this->system['app_name'];
        $streamName = $this->streamFormat($streamName);
        // $streamName = $this->system['play_app_id'] . '_' . $streamName;
        $key = $this->system['key'];

        if ($key && $time) {
            // 推流有效到时间
            $txTime = strtoupper(base_convert(strtotime($time), 10, 16));
            $txSecret = md5($key . $streamName . $txTime);
            $extStr = '?' . http_build_query(array(
                'txSecret' => $txSecret,
                'txTime' => $txTime,
            ));
        } else {
            $extStr = '';
        }
        $push = "rtmp://{$domain}/{$appName}/{$streamName}{$extStr}";
        $pull[] = "http://{$playDomain}/{$appName}/{$streamName}.flv{$extStr}";
        $pull[] = "http://{$playDomain}/{$appName}/{$streamName}.m3u8{$extStr}";

        return [
            'push' => $push,
            'pull' => $pull,
        ];
    }

    /**
     * 查詢推斷流事件
     *
     * @param array $parameters
     * @return string
     */
    public function history(array $parameters = []):  ? string
    {
        $this->initWithConfig();
        try {
            $cred = new Credential($this->system['secret_id'], $this->system['secret_key']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->system['end_point']);

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, $this->system['locale'], $clientProfile);

            $req = new DescribeLiveStreamEventListRequest();
            $req->fromJsonString(json_encode($parameters));

            $resp = $client->DescribeLiveStreamEventList($req);

            return $resp->toJsonString();
        } catch (TencentCloudSDKException $exception) {
            \Log::warning($exception->getMessage());
        }
    }

    /**
     * 斷開直播流
     *
     * @param array $parameters
     * @return string
     */
    public function cut(array $parameters = []) : string
    {
        $this->initWithConfig();
        try {
            $parameterStreamName = ($parameters['stream_name']) ?? '';
            if (!$parameterStreamName) {
                throw new \Exception(__('liveRoom.cut_error_not_assign_stream_name'));

            }
            $cred = new Credential($this->system['secret_id'], $this->system['secret_key']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->system['end_point']);

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, $this->system['locale'], $clientProfile);

            $req = new DropLiveStreamRequest();

            $streamName = $this->streamFormat($parameterStreamName);
            $requestData = [
                'Action' => 'DropLiveStream',
                'Version' => '2018-08-01',
                'AppName' => $this->system['app_name'],
                'DomainName' => $this->system['domain'],
                'StreamName' => $streamName,
            ];
            $req->fromJsonString(json_encode($requestData));

            $resp = $client->DropLiveStream($req);

            return $resp->toJsonString();
        } catch (TencentCloudSDKException $exception) {
            \Log::warning($exception->getMessage());
            throw $exception;
        }
    }

    /**
     * 查詢正在直播資訊
     *
     * @param array $parameters
     * @return string
     */
    public function search(array $parameters = []): string
    {
        $this->initWithConfig();
        try {
            $cred = new Credential($this->system['secret_id'], $this->system['secret_key']);
            $httpProfile = new HttpProfile();
            $httpProfile->setEndpoint($this->system['end_point']);

            $clientProfile = new ClientProfile();
            $clientProfile->setHttpProfile($httpProfile);
            $client = new LiveClient($cred, $this->system['locale'], $clientProfile);

            $req = new DescribeLiveStreamOnlineListRequest();
            $parameterStreamName = ($parameters['stream_name']) ?? '';
            $streamName = $this->streamFormat($parameterStreamName);
            $requestData = [
                'Action' => 'DescribeLiveStreamState',
                'AppName' => $this->system['app_name'],
                'DomainName' => $this->system['domain'],
                'StreamName' => $streamName,
                'Version' => '2018-08-01',
            ];
            $req->fromJsonString(json_encode($requestData));

            $resp = $client->DescribeLiveStreamOnlineList($req);

            return $resp->toJsonString();
        } catch (TencentCloudSDKException $exception) {
            \Log::warning($exception->getMessage());
        }
    }

    /**
     * 取得live 查询的client
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T10:39:07+0800
     */
    protected function getLiveClient()
    {
        $this->initWithConfig();
        $cred = new Credential($this->system['secret_id'], $this->system['secret_key']);
        $httpProfile = new HttpProfile();
        $httpProfile->setEndpoint($this->system['end_point']);

        $clientProfile = new ClientProfile();
        $clientProfile->setHttpProfile($httpProfile);
        $client = new LiveClient($cred, $this->system['locale'], $clientProfile);
        return $client;
    }
    /**
     * 查詢直播推流
     *
     * @param    array                    $parameters [description]
     *
     * @return   [type]                               [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-30T11:50:56+0800
     */
    public function query(array $parameters = []): string
    {
        $this->initWithConfig();
        try {
            $client = $this->getLiveClient();

            $req = new DescribeLiveStreamStateRequest();
            $parameterStreamName = ($parameters['stream_name']) ?? '';
            $streamName = $this->streamFormat($parameterStreamName);
            $requestData = [
                'Action' => 'DescribeLiveStreamState',
                'AppName' => $this->system['app_name'],
                'DomainName' => $this->system['domain'],
                'StreamName' => $streamName,
                'Version' => '2018-08-01',
            ];
            $req->fromJsonString(json_encode($requestData));

            $resp = $client->DescribeLiveStreamState($req);

            return $resp->toJsonString();
        } catch (TencentCloudSDKException $exception) {
            \Log::warning($exception->getMessage());
            throw $exception;

        }
    }

    protected function streamFormat($name)
    {
        $this->initWithConfig();
        return $this->system['play_app_id'] . '_' . $name;
    }
    /**
     * Lock 前置词
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T10:09:35+0800
     */
    public function getSignLockPrefix()
    {
        $tencentConfig = getTencentLiveConfigFromCache()['system'];
        return self::SIGN_CACHE_PRE_FIX . $tencentConfig['play_app_id'];
    }

    public function getInActiveString()
    {
        return self::INVALID_STRING;
    }
}
