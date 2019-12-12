<?php
namespace App\Services;

use App\Repositories\Interfaces\SystemConfigRepository;

//系统设定服务
class SystemConfigService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(SystemConfigRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save($id, $data)
    {
        return $this->repository->update($data, $id);
    }

    public function insert($data)
    {
        return $this->repository->create($data);
    }
}
