<?php
namespace App\Services;

use App\Repositories\Interfaces\ActivityAdRepository;

// 活动服务
class ActivityAdService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    public function __construct(ActivityAdRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save($id, $data)
    {
        try {
            $data = $this->processCoverUpload($data);
            $rollAd = $this->repository->find($id);
            $originPhotoPath = $rollAd->cover;
            $return = $rollAd->update($data);
            if (isset($data['cover'])) {
                \CLStorage::delete(decodeStoragePath($originPhotoPath));
            }
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    protected function processCoverUpload($data)
    {
        if (isset($data['cover'])) {
            $ext = $data['cover']->getClientOriginalExtension();
            $photoPath = $this->repository->makeModel()::COVER_PATH_PREFIX;
            $url = \CLStorage::upload($photoPath, $data['cover']);
            $data['cover'] = $url;
        }
        return $data;
    }
    public function insert($data)
    {
        $data = $this->processCoverUpload($data);
        return $this->repository->create($data);
    }
}
