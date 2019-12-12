<?php

namespace App\Services\Payments\Instances\JPAY;

use App\Exceptions\QueryException;
use App\Services\Payments\BasePayment;
use App\Services\Payments\Instances\JPAY\Rsa2Client;

class JPAYCommon extends BasePayment
{
    protected $config;
    protected $notifyUrl;
    protected $payType = 0;
    public function __construct()
    {
        $this->setConfig();
        $this->notifyUrl = externalRoute('web.notify.index', ['channel' => 'JPAY']);
    }
    /**
     * 设定设定档
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:20:07+0800
     */
    public function setConfig()
    {
        app()->configure('payments.JPAY');
        $this->config = config('payments.JPAY');
        return $this;
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

    /**
     * 取得签名
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T11:03:28+0800
     */
    protected function getSign($data)
    {
        $rsaClient = app(Rsa2Client::class, ['platformPubKey' => $this->config['public_key'], 'clientPrivateKey' => $this->config['private_key']]);
        $sign = $rsaClient->createSign($data);
        return $sign;
    }

    /**
     * 验证订单状态
     *
     * @param    [type]                   $ret [description]
     *
     * @return   [type]                        [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T11:05:23+0800
     */
    protected function validateResponse($ret, $payload)
    {
        $jsonResult = json_decode($ret, true);
        $requiredField = ['error', 'msg', 'data'];
        //回传的不是JSON格式
        if (json_last_error()) {
            throw new QueryException(__('payChannelPayment.error.not_valid_api_json_response', ['response' => $ret]), 1, $payload);
        }
        //回传的格式跟 文件 不一致
        foreach ($requiredField as $key => $value) {
            if (!array_key_exists($value, $jsonResult)) {
                throw new QueryException(__('payChannelPayment.error.not_valid_api_response', ['response' => $ret]), 1, $payload);
            }
        }
        // 回传 错误讯息
        if (intval($jsonResult['error']) != 0) {
            throw new QueryException(__('payChannelPayment.error.valid_api_error', ['response' => $jsonResult['msg']]), 1, $payload);
        }
    }

    /**
     * 统一在这里呼叫API
     *
     * @param    [type]                   $data [description]
     * @param    [type]                   $path [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T11:06:54+0800
     */
    protected function callAPI($data, $path)
    {
        //for test
        if ((config('app.env') != 'production') && $path == 'query') {
            //不去真的访问
            $data = [
                'status' => 'SUCCESS',
                'money' => $data['money'],
                'amount' => $data['money'],
                'out_order_id' => $data['order_id'],
                'order_id' => $data['order_id'],
                'fee' => $data['money'] * 0.04,
            ];
            return $data;
        }
        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => $this->config['host'] . 'api/platform/pay/' . $path,
            CURLOPT_HEADER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
        ];
        curl_setopt_array($curl, $curlOptions);
        $ret = curl_exec($curl);
        curl_close($curl);
        $this->validateResponse($ret, $curlOptions);
        $jsonResult = json_decode($ret, true);
        $data = $jsonResult['data'];
        return $data;
    }

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
    public function pay(array $request)
    {

        $apiData = [
            'merchant_id' => $this->config['merchant_id'],
            'out_order_id' => $request['transaction_no'],
            'money' => $request['amount'] * 100,
            'pay_type' => $this->payType,
            'notify_url' => $this->notifyUrl,
            'remark' => 'success',
        ];
        $sign = $this->getSign($apiData);
        $apiData['sign'] = $sign;
        //统一透过这个function 输出结果
        $result = $this->sendToRemote($apiData);
        return $this->outputPayResult($result['url'], $result['order_id']);
    }

    private function sendToRemote($data)
    {
        return $this->callAPI($data, 'add');
    }
}
