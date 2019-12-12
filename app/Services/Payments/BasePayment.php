<?php

namespace App\Services\Payments;

use App\Models\UserTopupOrder;
use App\Services\Payments\BasePaymentCommon;

class BasePayment extends BasePaymentCommon
{
    //返回连结类型
    const CALLBACK_TYPE_LINK = 'link'; //一般超连结
    const CALLBACK_TYPE_HTML = 'html'; // HTML
    const CALLBACK_TYPE_QRCODE = 'qrcode'; //QRCODE
    //返回方式 qrcode 或是 link
    protected $callBackType;

    protected $notifyRequest;
    protected $order = null;
    /**
     * 异步回调的栏位对照资料库交易栏位
     *
     * 异步资料栏位 对应 资料库交易栏位
     * 一定要有 资料库交易栏位transaction_no 的对应，不然无法反向更新到DB
     * @var array
     */
    protected $notifyColumnMap = [];

    /**
     * 统一透过它输出付费资讯
     *
     * @param    string                   $link [description]
     * @param    string                   $orderNo 订单编号
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:17:41+0800
     */
    public function outputPayResult($link, $orderNo = '')
    {
        return app(PayResult::class, ['link' => $link, 'callBackType' => $this->callBackType, 'payTransactionNo' => $orderNo]);
    }

    /**
     * 回调请求栏位对照表
     *
     * @param    [type]                   $source [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:59:52+0800
     */
    protected function requestColumnMapping($source)
    {
        $return = [];
        foreach ($source as $key => $value) {
            if (isset($this->notifyColumnMap[$key])) {
                $return[$this->notifyColumnMap[$key]] = $value;
            }
        }
        return $return;
    }

    /**
     * 返回连结类型
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T09:54:19+0800
     */
    public function getCallBackType()
    {
        return $this->callBackType;
    }

    /**
     * 设定当前的交易订单资料
     *
     * @param    UserTopupOrder           $model [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T11:38:40+0800
     */
    public function setOrderData(UserTopupOrder $model)
    {
        $this->order = $model;
        return $this;
    }

    /**
     * 验证栏位是否都存在
     *
     * @param    [type]                   $requiredColumn [description]
     * @param    [type]                   $request        [description]
     *
     * @return   [type]                                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-20T09:59:11+0800
     */
    public function requiredColumn($requiredColumn, $request)
    {
        foreach ($requiredColumn as $key => $value) {
            if (!isset($request[$value])) {
                throw new \Exception(__('payChannelPayment.error.not_valid_api_notify_request', ['request' => json_encode($request)]));
            }
        }
    }

    /**
     * 异步回调的栏位对照付费栏位
     *
     * @var array
     */
    public function notify($request, $callback = '')
    {
        try {
            if (is_callable($callback)) {
                $callback($request);
            }
            return $this->notifyCallbackResult($formatRequest);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }
    /**
     * 查询订单
     *
     * @param    UserTopupOrder           $order [description]
     *
     * @return   Array                          格式化后的 外部订单格式
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T11:23:11+0800
     */
    public function queryOrder(UserTopupOrder $order)
    {
        $response = $this->queryOrderByAPI($order);
        return $this->formatNotifyRequest($response);
    }

}
