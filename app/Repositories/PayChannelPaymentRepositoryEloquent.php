<?php

namespace App\Repositories;

use App\Models\PayChannelPayment;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\payChannelPaymentRepository;

/**
 * Class PayChannelPaymentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PayChannelPaymentRepositoryEloquent extends BaseRepository implements PayChannelPaymentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PayChannelPayment::class;
    }

    /**
     * 该笔支付方式目前是否可以进行储值交易
     *
     * @param    PayChannelPayment        $model [description]
     * @param    array        $request 外部传入参数
     *
     * @return   boolean                         true
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T10:59:37+0800
     */
    public function isCanPay(PayChannelPayment $model, $request)
    {
        if ($model->available != PayChannelPayment::AVAILABLE_ENABLE) {
            throw new \Exception(__('payChannelPayment.error.is_not_available'));
        }
        //检查输入金额
        if ($model->custom_amount == PayChannelPayment::CUSTOMAMOUNT_NO) {
            //只要不支援客户自行输入金额，就一律检查有没有在面额表设定里
            $order_amounts = explode(',', $model->order_amounts);
            if (!($order_amounts && in_array($request['amount'], $order_amounts))) {
                throw new \Exception(__('payChannelPayment.error.not_valid_amount'));
            }
        }
        return true;
    }

    /**
     * 计算手续费
     *
     * @param    PayChannelPayment        $model  [description]
     * @param    [type]                   $amount [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-20T11:48:37+0800
     */
    public function getCost(PayChannelPayment $model, $amount)
    {
        $fee = $model->fee * $amount;
        return $fee;
    }
}
