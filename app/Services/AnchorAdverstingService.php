<?php

namespace App\Services;

use App\Repositories\Interfaces\AnchorAdverstingRepository;

//热门主播
class AnchorAdverstingService
{
    protected $repository;
    public function __construct(AnchorAdverstingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
    }

    public function all()
    {
        return $this->repository->paginate();
    }

    public function allSkipCriteria($columns = ['*'])
    {
        return $this->repository->skipCriteria(true)->all($columns);
    }

    public function store($data)
    {
        $this->repository->create($data);
    }
}
