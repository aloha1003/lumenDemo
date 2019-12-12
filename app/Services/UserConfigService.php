<?php
namespace App\Services;

use App\Repositories\Interfaces\UserConfigRepository;

//用户设定服务
class UserConfigService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(UserConfigRepository $repository)
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
