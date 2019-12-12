<?php
namespace App\Services;

use App\Repositories\Interfaces\RollAdHitRecordRepository;
use App\Repositories\Interfaces\RollAdRepository;
use Carbon\Carbon;

//轮播广告服务
class RollAdService
{
    use \App\Traits\MagicGetTrait;
    use \App\Traits\PlatformMapIdTrait;
    private $repository;
    public function __construct(RollAdRepository $repository)
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
            $data = $this->processCoverUpload($data);
            $rollAd = $this->repository->find($id);
            $rollAd->admin_id = adminId();
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

    /**
     * 处理图片上传
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   array                   返回上传成功的输入资料
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T15:25:31+0800
     */
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
        $data = $this->processCoverUpload($data);
        $data['admin_id'] = adminId();
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
        $columns = ['title', 'target', 'cover', 'id', 'content', 'href', 'finish_at'];
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

    public function hitUrlById($id)
    {
        $ad = $this->repository->find($id);
        \Queue::pushOn(config('queue.connections.' . config('queue.default') . '.queue'), new \App\Jobs\RollAdHitCounter(id(), $id));
        return $ad->href;
    }

    /**
     * 連結計算
     *
     * @param    integer                   $userId 用戶id
     * @param    integer                   $rollAdId 廣告id
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-14T11:52:22+0800
     */
    public function hitCounter($userId, $id)
    {
        $rollAdHitRecordRepository = app(RollAdHitRecordRepository::class);
        $where = [
            'user_id' => $userId,
            'roll_ad_id' => $id,
        ];
        $hitRecords = $rollAdHitRecordRepository->findWhere($where);
        if ($hitRecords->count() == 0) {
            $insertData = $where;
            $insertData['hit'] = 1;
            $rollAdHitRecordRepository->create($insertData);
        } else {
            $record = $hitRecords->first();
            $record->hit = $record->hit + 1;
            $record->save();
        }
    }
}
