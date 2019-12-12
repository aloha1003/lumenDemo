<?php
namespace App\Services;

use App\Repositories\Interfaces\PayChannelRepository;

//交易渠道服务
class PayChannelService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(PayChannelRepository $repository)
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
     * 所有资料
     *
     * @param    array                    $columns 要选择的栏位，预设全部
     *
     * @return   collect                            [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-18T16:03:43+0800
     */
    public function all($columns = ['*'])
    {
        return $this->repository->all($columns);
    }
}
