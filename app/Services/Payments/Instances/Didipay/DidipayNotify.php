<?php

namespace App\Services\Payments\Instances\Didipay;

use App\Models\UserTopupOrder;
use App\Services\Payments\ConfigInterface;
use App\Services\Payments\Instances\Didipay\DidipayCommon;
use App\Services\Payments\NotifyInterface;

class DidipayNotify extends DidipayCommon implements NotifyInterface, ConfigInterface
{
    /**
     * 异步回调的栏位对照资料库交易栏位
     *
     * 异步资料栏位 对应 资料库交易栏位
     * 一定要有 资料库交易栏位transaction_no 的对应，不然无法反向更新到DB
     * @var array
     */
    protected $notifyColumnMap = [
        'total_amount' => 'amount',
        'out_order_id' => 'transaction_no',
        'trade_no' => 'pay_transaction_no',
    ];
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
        $requiredColumn = ['out_trade_no', 'sign', 'trade_no', 'open_id', 'total_amount', 'times'];
        $this->requiredColumn($requiredColumn, $request);
        //验证签名
        $verify = $this->verifySign($request, $request['sign']);

        if (!$verify) {
            throw new \Exception(__('payChannelPayment.error.signature_error'));
        }
        //验证交易状态是否成功
        if ($data['times']) {
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
        $formatRequest = $this->requestColumnMapping($request);
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
        //因为该支付不提供查询，正式环境回传失败
        if (config('app.env') != 'production') {
            $data = [
                'total_amount' => $order['total_amount'],
                'money' => $order['money'],
                'order_id' => $order['order_id'],
            ];
            $response = $this->callAPI($data, 'query');
            return ['data' => json_encode($response)];
        } else {
            return ['data' => json_encode([])];
        }
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
        //因为该支付并不能手动查询订单，正式环境一律回传 false, 避免误触
        return (config('app.env') != 'production');
    }
}
