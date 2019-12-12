<?php
namespace App\Services;

use App\Repositories\Interfaces\GoldTopupApplicationRepository;

//金币提现服务
class GoldTopupApplicationService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(GoldTopupApplicationRepository $repository)
    {
        $this->repository = $repository;
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
            $application = $this->repository->find($id);
            $application->admin_id = adminId();
            $return = $application->update($data);
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
     * @return   RollAd                         新增成功的广告
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:26:28+0800
     */
    public function insert($data)
    {
        $data['application_admin_id'] = adminId();
        return $this->repository->create($data);
    }
}
