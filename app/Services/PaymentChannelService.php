<?php
namespace App\Services;

use App\Repositories\Interfaces\PaymentChannelRepository;

// 交易
class PaymentChannelService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(PaymentChannelRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 新增
     *
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-10T16:03:27+0800
     */
    public function insert($data)
    {
        $newRecord = $this->repository->create($data);
        return $newRecord;
    }

    public function save($id, $data)
    {
        try {
            $item = $this->repository->find($id);
            $return = $item->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 计算手续费
     *
     * @param    string        $type [description]
     *
     * @param    double        $gold [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-12T10:39:38+0800
     */
    public function feeCalcuate($type, $gold)
    {
        //取得手续费设定
        $paymentChannels = $this->repository->findWhere(['slug' => $type]);
        if ($paymentChannels->count() == 0) {
            throw new \Exception(__('paymentChannel.not_found_records'));
        }
        $paymentChannel = $paymentChannels->first();
        $fee = 0;
        switch ($paymentChannel->fee_type) {
            case $paymentChannel::FEE_TYPE_FIX:
                $fee = $paymentChannel->fee;
                break;
            case $paymentChannel::FEE_TYPE_RATE:
                $fee = $gold * $paymentChannel->fee;
                break;
            case $paymentChannel::FEE_TYPE_STEP:
                $fee = $paymentChannel->fee;
                break;
            default:
                throw new \Exception(__('paymentChannel.unexpceted_fee_type'));
                break;
        }
        return $fee;
    }
}
