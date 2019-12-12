<?php

namespace App\Repositories;

use App\Models\UserTopupOrder;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\userTopupOrderRepository;

/**
 * Class UserTopupOrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTopupOrderRepositoryEloquent extends BaseRepository implements UserTopupOrderRepository
{

    protected $fieldSearchable = [
        'pay_channel_payments_pay_type',
        'user_id',
        'pay_at',
        'pay_step',
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTopupOrder::class;
    }

    /**
     * 产生 transaction_no
     *
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T11:23:36+0800
     */
    public function generateTransactionNo($userId)
    {
        $prefix = env('TOPUP_ORDER_NO_PREFIX', 'GYL') . 'I';
        $time = time();
        $date = date("YmdHis", $time);
        $milliseconds = round(microtime(true) * 1000) - $time * 1000;
        $no = $prefix . $date . $milliseconds . strtoupper(substr(uniqid(), 9));
        return $no;
    }

}
