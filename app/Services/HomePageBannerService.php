<?php
namespace App\Services;

use App\Repositories\Interfaces\HomePageBannerRepository;
use Carbon\Carbon;

//首页广告服务
class HomePageBannerService
{
    use \App\Traits\MagicGetTrait;
    use \App\Traits\PlatformMapIdTrait;
    private $repository;
    public function __construct(HomePageBannerRepository $repository)
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

    /**
     * 根据平台 取得广告，
     *
     * @param    [type]                   $platform [description]
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-03T09:24:58+0800
     */
    public function getAdByPlatform($platform)
    {
        $platformList = $this->getPlatformIdList($platform);
        $now = date("Y-m-d H:i:s", time());
        $today = date("Y-m-d", time());
        $columns = ['title', 'target', 'cover', 'href', 'content', 'finish_at'];
        $where = [
            ['start_at', '<=', $now],
            'status' => $this->repository->makeModel()::STATUS_YES,
        ];
        $result = $this->repository
            ->scopeQuery(function ($query) use ($today, $platformList) {
                return $query->where(function ($query) use ($today) {
                    $query->where('finish_at', '>=', $today);
                    $query->orWhere('finish_at', '=', '');
                    $query->orWhereNull('finish_at');
                })
                    ->whereIn('platform', $platformList)
                    ->orderBy('weight', 'asc')
                ;
            })
            ->findWhere($where, $columns)
            ->filter(function ($item) use ($now) {
                $startAt = Carbon::parse($item->start_at);
                if ($item->finish_at) {
                    $finishAt = Carbon::parse($item->finish_at);
                } else {
                    $finishAt = '';
                }
                //記錄的時間 小於等於 當前時間 並且 ( 記錄結束時間 為空白或 記錄結束時間大於等於 當前時間)
                return $startAt->lte($now) && (!$finishAt || $finishAt->gte($now));
            })->values();
        return $result;
    }
}
