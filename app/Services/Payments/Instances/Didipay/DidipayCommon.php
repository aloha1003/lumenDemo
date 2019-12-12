<?php

namespace App\Services\Payments\Instances\Didipay;

use App\Exceptions\QueryException;
use App\Services\Payments\BasePayment;

class DidipayCommon extends BasePayment
{
    protected $config;
    protected $notifyUrl;
    protected $payWayCode;
    //支援支付的类型代码
    const PAY_WAY_CODE_WECHAT_SCAN = 1; //微信扫码
    const PAY_WAY_CODE_ALI_SCAN = 2; //支付宝扫码
    const PAY_WAY_CODE_WECHAT_CARD = 3; //微信转卡
    const PAY_WAY_CODE_ALI_CARD = 4; //支付宝转卡
    public function __construct()
    {
        $this->setConfig();
        $this->notifyUrl = externalRoute('web.notify.index', ['channel' => 'Didipay']);
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
        $this->config = config('payments.Didipay');
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
            'openId' => $this->config['merchant_id'],
            'orderNo' => $request['transaction_no'],
            'productName' => 'canary-' . $request['amount'],
            'orderPrice' => $request['amount'],
            'payWayCode' => $this->payWayCode,
            'orderIp' => $this->config['orderIp'],
            'notifyUrl' => $this->notifyUrl,
            'remark' => 'success',
        ];
        $apiData['sign'] = $this->getSign($apiData);
        $result = $this->sendToRemote($apiData);
        return $this->outputPayResult($result['payUrl'], $request['transaction_no']);
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
        $data = array_filter($data);
        ksort($data);
        unset($data['sign']);
        $query = '';
        foreach ($data as $key => $value) {
            if ($query) {
                $query .= '&';
            }
            $query .= $key . '=' . $value;
        }
        $query .= '&paySecret=' . $this->config['api_key'];
        $sign = strtoupper(md5($query));
        return $sign;
    }

    private function sendToRemote($data)
    {
        return $this->callAPI($data, 'add');
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
                'total_amount' => $data['total_amount'],
                'amount' => $data['money'],
                'out_trade_no' => $data['order_id'],
                'trade_no' => $data['order_id'],
                'open_id' => $this->config['merchant_id'],
            ];
            return $data;
        }
        $curl = curl_init();
        $curlOptions = [
            CURLOPT_URL => $this->config['url'],
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

    protected function validateResponse($ret, $payload)
    {

        $jsonResult = json_decode($ret, true);
        $requiredField = ['status', 'data'];
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
        if (intval($jsonResult['status']) != 0) {
            throw new QueryException(__('payChannelPayment.error.valid_api_error', ['response' => $jsonResult['message']]), 1, $payload);
        }
    }

    /**
     * 验证签名
     *
     * @param    array                    $data [description]
     * @param    string                   $sign [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-27T09:18:50+0800
     */
    public function verifySign($data = [], $sign = '')
    {
        $localSign = $this->getSign($data);
        return ($sign === $localSign);
    }
}
