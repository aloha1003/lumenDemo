<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * 第三方串接
 *
 */
class CurlApiService
{
    private $headers;
    private $client;
    private $domain;
    private $url;
    private $codeSuccess;
    private $response;

    /**
     * 需要讀取的config設定檔
     *
     * @param $config
     */
    public function __construct($config)
    {
        if (is_array($config)) {
            config($config);
            $this->headers = $config['headers'];
            $this->client = new Client(['handler' => $this->handlerStack($config['log_filename']), 'headers' => $this->headers]);
            $this->domain = $config['domain'];
            $this->codeSuccess = $config['code']['success'];
        } else {
            $configSetting = config($config);
            $this->headers = $configSetting['headers'];
            $this->client = new Client(['handler' => $this->handlerStack($configSetting['log_filename']), 'headers' => $this->headers]);
            $this->domain = $configSetting['domain'];
            $this->codeSuccess = $configSetting['code']['success'];
        }
    }

    /**
     * 紀錄log
     *  https://laravel-china.org/topics/3150/experience-the-use-of-error-and-log-services-on-laravel
     *
     * @return void
     */
    protected function handlerStack($filename)
    {
        $path = storage_path("/logs/curl-api/{$filename}.log");

        $logger = new Logger('CurlApiLog');
        $logger->pushHandler(new StreamHandler($path), Logger::DEBUG);

        $stack = HandlerStack::create();
        $stack->push(
            Middleware::log(
                $logger,
                new MessageFormatter('{"time" : "{date_common_log}", "url" : "{target}", "method" : "{method}", "parameters":{req_body}, "response":{res_body}}')
            )
        );
        return $stack;
    }

    public function setPath(string $path = '')
    {
        $this->url = str_finish($this->domain, '/') . $path;
        return $this;
    }

    public function get(array $parameters = [])
    {
        return $this->send('GET', ['query' => $parameters]);
    }

    public function post(array $parameters = [])
    {
        return $this->send('POST', ['json' => $parameters]);
    }

    public function put(array $parameters = [])
    {
        return $this->send('PUT', ['json' => $parameters]);
    }

    public function patch(array $parameters = [])
    {
        return $this->send('PATCH', ['json' => $parameters]);
    }

    public function delete(array $parameters = [])
    {
        return $this->send('DELETE', $parameters);
    }

    public function request(string $method = 'GET', $parameters)
    {
        return new Request($method, $this->url, $this->headers, $parameters);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    protected function send(string $method, array $parameters):  ? array
    {
        try {
            $result = $this->client->request($method, $this->url, $parameters);
            $this->response = $this->returnResult($result);
            return $this->response;
        } catch (RequestException $exception) {
            \Log::info(json_encode($exception->getRequest()));
            return null;
        } catch (ClientException $exception) {
            \Log::info(json_encode($exception->getRequest()));
            return null;
        } catch (ServerException $exception) {
            \Log::info(json_encode($exception->getRequest()));
            return null;
        } catch (\Exception $ex) {
            wl($ex);
            return null;
        }
    }

    protected function returnResult($response)
    {
        return !is_null($response) ? $this->parser($response) : null;
    }

    protected function parser($response) :  ? array
    {
        $message = $response->getBody();
        return json_decode($message, true);
    }

    public function isSuccessed() : bool
    {
        return $this->response && isset($this->response['code']) && $this->response['code'] == $this->codeSuccess;
    }

    /**
     * 发送请求
     *
     * @param    Request                  $request [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-16T10:45:54+0800
     */
    public function sendRequest($url, $body)
    {

        return $this->client->send($request);
    }
}
