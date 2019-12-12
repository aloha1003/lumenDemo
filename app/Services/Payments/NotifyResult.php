<?php

namespace App\Services\Payments;

/**
 * @SWG\Definition(
 *      definition="通知结果",
 *      @SWG\Property(
 *          property="link",
 *          type="string",
 *          description="付费连结"
 *      )
 * )
 */
class NotifyResult
{
    protected $order_id;
    protected $order_status;
    public function __construct(string $order_id, string $order_status)
    {
        $this->order_id = $order_id;
        $this->order_status = $order_status;
    }
    /**
     * 格式化输出结果
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T09:29:06+0800
     */
    public function formatResult()
    {
        return [
            'order_id' => $this->order_id,
            'order_status' => $this->order_status,
        ];
    }
}
