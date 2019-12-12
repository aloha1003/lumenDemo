<?php

namespace App\Services\Payments\Instances\JPAY;

use App\Models\UserTopupOrder;
use App\Services\Payments\ConfigInterface;
use App\Services\Payments\Instances\JPAY\JPAYCommon;
use App\Services\Payments\Instances\JPAY\Rsa2Client;
use App\Services\Payments\NotifyInterface;

class JPAYNotify extends JPAYCommon implements NotifyInterface, ConfigInterface
{
    const SUCCESS_STR = 'SUCCESS';
    protected $notifyColumnMap = [
        'money' => 'amount',
        'out_order_id' => 'transaction_no',
        'order_id' => 'pay_transaction_no',
        'fee' => 'cost',
    ];
    protected $callBackType = '';
    /**
     * 验证通知是否合法
     *
     * @param    array                    $formatRequest 已经格式化好的请求
     *
     * @return   void                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:06:44+0800
     */
    public function verifyNotify(array $request)
    {
        $requiredColumn = ['data', 'sign'];
        $this->requiredColumn($requiredColumn, $request);
        //验证签名
        $rsaClient = app(Rsa2Client::class, ['platformPubKey' => $this->config['public_key'], 'clientPrivateKey' => $this->config['private_key']]);
        $data = json_decode($request['data'], true);
        $verify = $rsaClient->verifySign($data, $request['sign']);

        if (!$verify) {
            throw new \Exception(__('payChannelPayment.error.signature_error'));
        }
        //验证data的栏位是否跟第三方文件一致
        $requiredDataColumn = [
            'fee',
            'finish_time',
            'merchant_id',
            'money',
            'order_id',
            'out_order_id',
            'pay_type',
            'status',
        ];
        $this->requiredColumn($requiredDataColumn, $data);
        //验证交易状态是否成功
        if ($data['status'] != self::SUCCESS_STR) {
            throw new \Exception(__('payChannelPayment.error.trasaction_not_finish'));
        }
    }
    /**
     * 格式化回调请求
     *
     * @param    array                    $request [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:06:14+0800
     */
    public function formatNotifyRequest(array $request)
    {
        //先做栏位对照
        $data = json_decode($request['data'], true);
        $formatRequest = $this->requestColumnMapping($data);
        //因为回传的是分
        $formatRequest['amount'] = $formatRequest['amount'] / 100;
        $formatRequest['cost'] = $formatRequest['cost'] / 100;
        return $formatRequest;
    }
    /**
     * 返回第四方异步通知，约定好的格式输出
     *
     * @param    array                    $request [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:39:41+0800
     */
    public function notifyCallbackResult(array $request)
    {
        die('success');
    }

    /**
     * 手动查询第三方订单状态
     *
     * @param    UserTopupOrder                    $order 当前订单
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T10:03:49+0800
     */
    public function queryOrderByAPI(UserTopupOrder $order)
    {
        if (config('app.env') != 'production') {
            $data = [
                'merchant_id' => $this->config['merchant_id'],
                'order_id' => $order->transaction_no,
                'out_order_id' => $order->transaction_no,
                'money' => $order->amount * 100,
            ];
        } else {
            $data = [
                'merchant_id' => $this->config['merchant_id'],
                'out_order_id' => $order->transaction_no,
            ];
        }
        $sign = $this->getSign($data);
        $data['sign'] = $sign;
        $response = $this->callAPI($data, 'query');

        return ['data' => json_encode($response)];
    }

    /**
     * 手动确认订单是否完成
     *
     * @param    UserTopupOrder                    $order 当前订单
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T10:52:31+0800
     */
    public function isOrderFinish(UserTopupOrder $order)
    {
        $order = $this->queryOrderByAPI($order);
        $order = json_decode($order['data'], true);
        $status = $order['status'] ?? false;
        return ($status == self::SUCCESS_STR);
    }
}
