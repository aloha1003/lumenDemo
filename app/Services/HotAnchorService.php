<?php

namespace App\Services;

use App\Repositories\Interfaces\HotAnchorRepository;

//热门主播服务
class HotAnchorService
{
    protected $repository;
    public function __construct(HotAnchorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function obtain($id)
    {
        return $this->repository->skipCriteria()->find($id);
    }

    public function all($query = [], $columns = ['*'])
    {
        if ($query) {
            $this->repository->injectSearch($query)->skipCriteria(false);
        }
        return $this->repository->paginate(null, $columns);
    }

    public function allSkipCriteria($columns = ['*'])
    {
        return $this->repository->skipCriteria(true)->findWhere(['role' => $this->repository->makeModel()::ROLE_MANAGER], $columns);
    }

    public function store($data)
    {
        if (isset($data['id'])) {
            $this->repository->update($data, $data['id']);
        } else {
            $this->repository->create($data);
        }
        return true;
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

}
