<?php

namespace App\Services;

use App\Repositories\Interfaces\NewAnchorRepository;

//新主播服务
class NewAnchorService
{
    protected $repository;
    public function __construct(NewAnchorRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository()
    {
        return $this->repository;
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

}
