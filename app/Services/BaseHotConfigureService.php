<?php
namespace App\Services;

use App\Repositories\Interfaces\BaseHotConfigureRepository;

/**
 * 热度设定服务
 */
class BaseHotConfigureService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(BaseHotConfigureRepository $repository)
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
        $model = $this->repository->find($id);
        $return = $model->update($data);
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   BaseHotConfigure               新增成功的BaseHotConfigure
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
}
