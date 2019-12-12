<?php
namespace App\Services;

use App\Repositories\Interfaces\FirstTopupRecordRepository;
use App\Repositories\Interfaces\UserTopupOrderRepository;
use App\Repositories\Interfaces\UserTopupReportRepository;

//充值报表服务
class UserTopupReportService
{
    use \App\Traits\MagicGetTrait;
    private $orderRepo;
    private $repository;
    private $firstTopupRecordRepository;
    public function __construct(UserTopupOrderRepository $orderRepo, UserTopupReportRepository $repository, FirstTopupRecordRepository $firstTopupRecordRepository)
    {
        $this->repository = $repository;
        $this->orderRepo = $orderRepo;
        $this->firstTopupRecordRepository = $firstTopupRecordRepository;
    }

    /**
     * 存档
     *
     * @param    id                   $id   主键
     * @param    array                   $data 输入资料
     *
     * @return   void                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:24:25+0800
     */
    public function save($id, $data)
    {
        try {
            $rollAd = $this->repository->find($id);
            $return = $rollAd->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   UserTopupReport               新增成功的UserTopupReport
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        return $this->repository->create($data);
    }
    /**
     * 删除资料
     *
     * @param    [type]                   $id [description]
     *
     * @return   [type]                       [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T13:15:14+0800
     */
    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    /**
     * 结算充值
     *
     * @param    [type]                   $date [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T15:27:06+0800
     */
    public function settlement($date)
    {

        try {
            //先把当天的资料删除
            $this->repository->deleteWhere(['settle_date' => $date]);
            $selects = ['pay_channels_slug',
                'pay_channel_payments_pay_type',
                \DB::raw('sum(cost) as cost'),
                \DB::raw('sum(amount) as amount'),
                \DB::raw('sum(profit) as profit'),
                \DB::raw('sum(gold) as gold'),
                \DB::raw('GROUP_CONCAT(distinct user_id) as users'),
                \DB::raw('GROUP_CONCAT( transaction_no) as transaction_nos'),
            ];
            $orderRepoModel = $this->orderRepo->makeModel();
            $result = $orderRepoModel->where(\DB::raw('DATE(pay_at)'), $date)
                ->select($selects)
                ->groupBy(['pay_channels_slug', 'pay_channel_payments_pay_type'])
                ->get();

            if ($result->count() > 0) {
                //取得当天有首充的
                $firstTopupModel = $this->firstTopupRecordRepository->makeModel();
                $firstTopup = $firstTopupModel->where(\DB::raw('DATE(created_at)'), $date)->select('transaction_no')->get();
                $transactionNoStr = $firstTopup->implode('transaction_no', ',');
                $transactionNos = explode(',', $transactionNoStr);
                //组合记录
                $result = $result->toArray();
                $insertData = [];
                $now = date("Y-m-d H:i:s", time());
                foreach ($result as $key => $value) {
                    $orderTransactionNos = explode(',', $value['transaction_nos']);
                    $interSects = array_intersect($orderTransactionNos, $transactionNos);
                    $insertData[] = [
                        'settle_date' => $date,
                        'pay_channels_slug' => $value['pay_channels_slug'],
                        'pay_channel_payments_pay_type' => $value['pay_channel_payments_pay_type'],
                        'cost' => $value['cost'],
                        'amount' => $value['amount'],
                        'profit' => $value['profit'],
                        'gold' => $value['gold'],
                        'no_repeat_users' => count(explode(',', $value['users'])),
                        'no_repeat_first_users' => count($interSects),
                        'created_at' => $now,
                    ];
                }
                $this->repository->makeModel()->insert($insertData);
            }
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }

    }
}
