<?php

namespace App\Services\Payments;

use App\Models\UserTopupOrder;

interface NotifyInterface
{
    /**
     * 验证通知是否合法
     *
     * @param    array                    $request [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:06:44+0800
     */
    public function verifyNotify(array $request);
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
    public function formatNotifyRequest(array $request);

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
    public function notifyCallbackResult(array $request);

    /**
     * 手动查询第三方订单
     *
     * @param    UserTopupOrder                    $order 当前订单
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T10:03:49+0800
     */
    public function queryOrderByAPI(UserTopupOrder $order);

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
    public function isOrderFinish(UserTopupOrder $order);
}
