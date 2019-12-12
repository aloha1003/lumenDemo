<?php
namespace App\Services;

use App\Repositories\Interfaces\AnnouceForUserRepository;
use App\Repositories\Interfaces\AnnouceRepository;
use Carbon\Carbon;

//公告服务
class AnnouceService
{
    use \App\Traits\MagicGetTrait;
    use \App\Traits\PlatformMapIdTrait;
    private $repository;
    private $annouceForUserRepository;

    public function __construct(AnnouceRepository $repository, AnnouceForUserRepository $annouceForUserRepository)
    {
        $this->repository = $repository;
        $this->annouceForUserRepository = $annouceForUserRepository;
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
            $rollAd = $this->repository->find($id);
            $originPhotoPath = $rollAd->cover;
            $return = $rollAd->update($data);
        } catch (\Exception $ex) {
            wl($ex);
            throw $ex;
        }
    }

    /**
     * 新增资料
     *
     * @param    array                   $data 原来的输入资料
     *
     * @return   Annouce                         新增成功的公告
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
     * 將公告設為已讀
     */
    public function read($userId, $isCommon, $announceId)
    {
        $model = null;
        if ($isCommon) {
            $model = $this->repository->findWhere([
                'id' => $announceId,
            ])->first();
        } else {
            $model = $this->annouceForUserRepository->findWhere([
                'id' => $announceId,
            ])->first();

        }
        if ($model != null) {
            $model->is_read = 1;
            $model->save();
        }
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
    public function getListByPlatform($platform)
    {
        $platformList = $this->getPlatformIdList($platform);
        $now = date("Y-m-d H:i:s", time());
        $today = date("Y-m-d", time());
        $columns = ['id', 'title', 'content', 'start_at', 'is_read', 'type_slug', 'finish_at'];
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
                    ->whereIn('platform', $platformList);
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
            }
            )->values()
        ;

        $userId = id();
        $singleAnnounceData = $this->annouceForUserRepository->findWhere([
            'user_id' => $userId,
        ])->all();

        for ($i = 0; $i < count($singleAnnounceData); $i++) {
            $data = $singleAnnounceData[$i];

            $result[] = [
                'id' => $data->id,
                'title' => $data->title,
                'content' => $data->content,
                'start_at' => Carbon::parse($data->created_at)->format('Y-m-d H:i:s'),
                'is_read' => $data->is_read,
                'type_slug' => $data->type_slug,
                'is_common' => 0,
            ];
        }
        for ($i = 0; $i < count($result); $i++) {
            if (isset($result[$i]['is_common']) == false) {
                $result[$i]['is_common'] = 1;
            }
        }
        $result = $result->toArray();
        // 依照日期做排序
        usort($result, function ($a, $b) {
            if ($a['start_at'] == $b['start_at']) {
                return 0;
            }
            return ($a['start_at'] < $b['start_at']) ? 1 : -1;
        });

        return $result;
    }
}
