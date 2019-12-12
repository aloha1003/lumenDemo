<?php
namespace App\Services;

use App\Repositories\Interfaces\PayChannelPaymentRepository;
use App\Repositories\Interfaces\SpecialUserRepository;

/**
 * 取得付费渠道
 */
class PayChannelPaymentService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    private $specialUserRepository;
    public function __construct(PayChannelPaymentRepository $repository, SpecialUserRepository $specialUserRepository)
    {
        $this->repository = $repository;
        $this->specialUserRepository = $specialUserRepository;
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
        try {
            $newRecord = $this->repository->create($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
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
     * 取得所有可用的支付方式
     *
     * @param    array                    $olumns [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-19T10:12:14+0800
     */
    public function allAvaialbePayments($columns = ['*'])
    {
        $model = app($this->repository->model());
        $orderByColumn = 'rank';
        $where = ['available' => $model::AVAILABLE_ENABLE];
        $result = $this->repository->scopeQuery(function ($query) use ($orderByColumn) {
            return $query->orderBy($orderByColumn, 'asc');
        })->findWhere($where, $columns)
        ;
        $iconService = app(\App\Services\PayTypeIconService::class);
        $allIcon = $iconService->getAllIcon();
        $result = $result->map(function ($item) use ($allIcon) {
            if (isset($item['order_amounts'])) {
                $item['order_amounts'] = explode(',', $item['order_amounts']);
            }
            if (isset($item['id'])) {
                $item['pay_id'] = $item['id'];
            }
            $item['icon'] = $allIcon[$item['pay_type']] ?? __('payTypeIcon.unset_icon');
            return $item;
        })->toArray();
        return $result;
    }
    /**
     * 取得所有资料
     *
     * @param    array                    $columns [description]
     *
     * @return   [type]                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-23T14:44:02+0800
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }

    /**
     * 取得测试人员
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-24T09:13:57+0800
     */
    public function getTestUser()
    {
        return $this->specialUserRepository->findWhere(['user_type' => $this->specialUserRepository->makeModel()::USER_TYPE_TEST])->toArray();
    }
}
