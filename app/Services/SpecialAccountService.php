<?php
namespace App\Services;

use App\Repositories\Interfaces\SpecialAccountRepository;

//内定帐号服务
class SpecialAccountService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(SpecialAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 修改资料
     *
     * @param    [type]                   $id   [description]
     * @param    [type]                   $data [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-05T15:28:27+0800
     */
    public function save($id, $data)
    {
        try {
            $record = $this->repository->find($id);
            $data['set_at'] = date("Y-m-d H:i:s", time());
            $return = $record->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    public function insert($data)
    {
        $data['set_at'] = date("Y-m-d H:i:s", time());
        return $this->repository->create($data);
    }
}
