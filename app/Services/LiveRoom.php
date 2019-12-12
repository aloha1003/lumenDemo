<?php

namespace App\Services;

use App\Models\BaseHotConfigure;
use App\Models\LiveRoom as BaseModel;
use App\Models\GameBetRecord as GameBetRecordModel;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\BaseBarrageTypeRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use App\Repositories\Interfaces\HotAnchorRepository;
use App\Repositories\Interfaces\LiveBarrageStatisticsRepository;
use App\Repositories\Interfaces\LiveGiftStatisticsRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\NewAnchorRepository;
use App\Repositories\Interfaces\UserConfigRepository;
use App\Repositories\Interfaces\UserFollowRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\GameBetRecordRepository;
use App\Services\LeaderboardService;
use App\Services\UserLevelService;
use App\Services\UserService;
use Carbon\Carbon;

//直播房服务
class LiveRoom
{
    private $repository;
    private $userfollowRepository;
    private $hotAnchorRepository;
    private $newAnchorRepository;
    private $userRepository;
    private $liveGiftStatisticsRepository;
    private $liveBarrageStatisticsRepository;
    private $gameBetRecordRepository;

    private $userConfigRepository;
    private $anchorInfoRepository;
    use \App\Traits\MagicGetTrait;
    const ROOM_QUERY_TYPE_GAME = 'game';
    const ROOM_QUERY_TYPE_HOT = 'hot';
    const ROOM_QUERY_TYPE_FOLLOW = 'follow';
    const ROOM_QUERY_TYPE_RECOMMEND = 'recommend';
    const ROOM_QUERY_TYPE_NEW = 'new';
    const ROOM_QUERY_TYPE_QUERY_ID = 'id';
    //進入房間鎖
    const LOCK_ENTER_ROOM_PREFIX_KEY = 'LOCK_ENTER_ROOM_PREFIX_KEY';
    const LOCK_LEAVE_ROOM_PREFIX_KEY = 'LOCK_LEAVE_ROOM_PREFIX_KEY';
    const ROOM_USER_ID_LIST_KEY = 'room_user_list:';

    const HOT_INFO_CACHE_KEY = 'hot_info';

    //計算熱度，隊列延遲時間
    const HOT_CALCULATOR_DELAY_TIME = 10;
    //送禮熱度隊列
    const GIFT_HOT_LIST_CACHE_KEY_PREFIX = 'gift_hot_list_';
    //正在执行热度更新的QUEUE
    const HOT_CALCULATOR_QUEUE_CACHE = 'hot_calculator_queue_';
    public function __construct(
        LiveRoomRepository $repository,
        UserFollowRepository $userfollowRepository,
        UserConfigRepository $userConfigRepository,
        UserRepository $userRepository,
        HotAnchorRepository $hotAnchorRepository,
        NewAnchorRepository $newAnchorRepository,
        AnchorInfoRepository $anchorInfoRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        BaseBarrageTypeRepository $baseBarrageTypeRepository,
        GameBetRecordRepository $gameBetRecordRepository,
        LiveGiftStatisticsRepository $liveGiftStatisticsRepository,
        LiveBarrageStatisticsRepository $liveBarrageStatisticsRepository) {
        $this->repository = $repository;
        $this->userfollowRepository = $userfollowRepository;
        $this->userRepository = $userRepository;
        $this->userConfigRepository = $userConfigRepository;
        $this->anchorInfoRepository = $anchorInfoRepository;
        $this->hotAnchorRepository = $hotAnchorRepository;
        $this->newAnchorRepository = $newAnchorRepository;
        $this->baseGiftTypeRepository = $baseGiftTypeRepository;
        $this->gameBetRecordRepository = $gameBetRecordRepository;
        $this->baseBarrageTypeRepository = $baseBarrageTypeRepository;
        $this->liveGiftStatisticsRepository = $liveGiftStatisticsRepository;
        $this->liveBarrageStatisticsRepository = $liveBarrageStatisticsRepository;
    }

    /**
     * 取得收禮統計資訊
     */
    public function getGiftStatistics($roomId)
    {
        $allGiftModelArray = $this->baseGiftTypeRepository->all();
        $allBarrageModelArray = $this->baseBarrageTypeRepository->all();

        $allGiftRecordArray = $this->liveGiftStatisticsRepository->findWhere(['room_id' => $roomId]);

        $allBarrageRecordArray = $this->liveBarrageStatisticsRepository->findWhere(['room_id' => $roomId]);

        $giftResult = [];
        $barrageResult = [];
        $length = count($allGiftModelArray);
        for ($i = 0; $i < $length; $i++) {
            $giftModel = $allGiftModelArray[$i];
            $giftResult[$giftModel['type_slug']] = [
                'name' => $giftModel['name'],
                'price' => $giftModel['gold_price'],
                'count' => 0,
                'total_gold' => 0,
            ];
        }

        $length = count($allBarrageModelArray);
        for ($i = 0; $i < $length; $i++) {
            $barrageModel = $allBarrageModelArray[$i];
            $barrageResult[$barrageModel['id']] = [
                'name' => $barrageModel['name'],
                'price' => $barrageModel['gold_price'],
                'count' => 0,
                'total_gold' => 0,
            ];
        }

        $length = count($allGiftRecordArray);
        for ($i = 0; $i < $length; $i++) {
            $giftRecord = $allGiftRecordArray[$i];
            $giftResult[$giftRecord['gift_type_slug']] = [
                'name' => $giftRecord['gift_name'],
                'price' => $giftRecord['gift_price'],
                'count' => $giftRecord['count'],
                'total_gold' => $giftRecord['gift_price'] * $giftRecord['count'],
            ];
        }

        $length = count($allBarrageRecordArray);
        for ($i = 0; $i < $length; $i++) {
            $barrageRecord = $allBarrageRecordArray[$i];
            $barrageResult[$barrageRecord['barrage_id']] = [
                'name' => $barrageRecord['barrage_name'],
                'price' => $barrageRecord['barrage_price'],
                'count' => $barrageRecord['count'],
                'total_gold' => $barrageRecord['barrage_price'] * $barrageRecord['count'],
            ];
        }

        $result = array_merge($barrageResult, $giftResult);
        // 排序 : 總收禮 > 數量 > 價格
        usort($result, function ($a, $b) {
            if ($a['total_gold'] == $b['total_gold']) {
                if ($a['count'] == $b['count']) {
                    if ($a['price'] == $b['price']) {
                        return 0;
                    }
                    return ($a['price'] < $b['price']) ? 1 : -1;
                }
                return ($a['count'] < $b['count']) ? 1 : -1;
            }
            return ($a['total_gold'] < $b['total_gold']) ? 1 : -1;
        });

        return $result;
    }

    /**
     * 取得直播室
     *
     * @param array $filter
     * @return void
     */
    public function getRooms($queryType = "game", $queryValue = "", $offset = 1, $followUserIdList = [])
    {
        $enableStatus = $this->repository->model()::STATUS_LIVE;
        $where = [
            'status' => $enableStatus,
            // 'password' => '',
        ];
        $orderByColumn = 'created_at';
        $whereIn = [];
        $weightMap = [];
        $weightKey = 'weight';
        if (!$followUserIdList) {
            $followUserIdList = $this->userfollowRepository->skipCache(true)->findWhere(['user_id' => id()], ['follow_uid'])->pluck('follow_uid')->toArray();
        }
        $hotInfoData = [];
        $pageOffset = $offset;
        $limit = config('app.api_per_page_data');
        switch ($queryType) {

            case self::ROOM_QUERY_TYPE_HOT:
                if ($pageOffset != 1) {
                    return [];
                }
                //排行榜的直播房资料
                $redisKey = LeaderboardService::LEADERBOARD_CACHE_KEY . ':' . LeaderboardService::LEADERBOARD_HOT_LiVE_ROOM_CACHE_KEY;
                $hotInfoData = \Cache::get($redisKey);
                if ($hotInfoData == null) {
                    $hotInfoData = [];
                }
                $userIdList = [];
                for ($i = 0; $i < count($hotInfoData); $i++) {
                    $userIdList[] = $hotInfoData[$i]['user_id'];
                }
                $orderByColumn = 'hot_value';
                $weightMap = $this->hotAnchorRepository->all(['user_id', 'weight'])->keyBy('user_id')->toArray();
                if ($userIdList == []) {
                    $userIdList = array_keys($weightMap);
                }
                $whereIn['user_id'] = $userIdList;
                $limit = LeaderboardService::LEADERBOARD_MAX_USER_NUMBER;
                break;
            case self::ROOM_QUERY_TYPE_FOLLOW:
                if (!$followUserIdList) {
                    return [];
                }
                $whereIn['user_id'] = $followUserIdList;
                break;
            case self::ROOM_QUERY_TYPE_GAME:
                if ($queryValue) {
                    $where['game_slug'] = $queryValue;
                }
                break;
            case self::ROOM_QUERY_TYPE_QUERY_ID:
                $where['id'] = $queryValue;
                break;
            case self::ROOM_QUERY_TYPE_NEW:
                $weightMap = $this->newAnchorRepository->all(['user_id', 'weight'])->keyBy('user_id')->toArray();
                $userIdList = array_keys($weightMap);
                $whereIn['user_id'] = $userIdList;
                break;
            default:
                $this->repository->setOrderBy('created_at')->setSortedBy('desc');
                break;
        }
        $where['status'] = $enableStatus;
        $pageOffset = ($pageOffset - 1) * $limit;

        // $this->repository->set()
        $result = $this->repository->with(['game' => function ($query) use ($enableStatus) {
            $query->where('status', '=', $enableStatus);
        }])
            ->scopeQuery(function ($query) use ($whereIn, $orderByColumn, $limit, $pageOffset, $where) {
                return $query->orderBy($orderByColumn, 'desc')->offset($pageOffset)->limit($limit);
            })
            ->findWhere($where);
        if ($whereIn) {
            foreach ($whereIn as $key => $value) {
                $result = $result->whereIn($key, $value);
            }
        }
        $result = $result->toArray();
        if ($result) {
            //根据权重排序
            if ($weightMap) {
                $result = collect($result)->map(function ($item, $key) use ($weightMap, $weightKey) {
                    $item[$weightKey] = ($weightMap[$item['user_id']]['weight']) ?? $key;
                    return $item;
                })->sortBy($weightKey)->toArray();
                $result = array_values($result);
            }
            //加入追随
            $result = collect($result)->map(function ($item) use ($followUserIdList) {
                $item['is_follow'] = in_array($item['user_id'], $followUserIdList);
                return $item;
            })->toArray();
        }
        //修正快取热度数值 ，因为db快取的hot_value不准，要依照排行榜的
        if ($queryType == self::ROOM_QUERY_TYPE_HOT) {
            $result = $this->mergeHotLiveRommWithCache($result, $hotInfoData);
        }
        $result = array_slice($result, ($offset - 1), config('app.api_per_page_data'));

        // 將熱門直播名次加到直播資料裡
        $leaderboardService = app(LeaderboardService::class);
        $hotRoomData = $leaderboardService->getHotLiveRoomLeaderboardData();
        if ($hotRoomData != null) {
            for ($i = 0; $i < count($hotRoomData); $i++) {
                $hotRoomData[$i]['rank'] = $i + 1;
            }

            $hotRoomData = collect($hotRoomData)->keyBy('room_id');
        }
        foreach ($result as $index => $data) {
            $roomId = $result[$index]['id'];
            if (isset($hotRoomData[$roomId])) {
                $result[$index]['rank'] = $hotRoomData[$roomId]['rank'];
            } else {
                $result[$index]['rank'] = -1;
            }
        }

        // 取得所有主播的id
        $allIds = [];
        foreach ($result as $data) {
            $allIds[] = $data['user_id'];
        }
        $userService = app(UserService::class);

        if ($queryType == self::ROOM_QUERY_TYPE_FOLLOW) {
            $allFollowUserInfo = $userService->getUserInfoByIds($followUserIdList);
            $noOnlineUserList = [];
            foreach ($allFollowUserInfo as $followUserInfo) {
                if ($followUserInfo['is_anchor'] == 1 && !in_array($followUserInfo['user_id'], $allIds)) {
                    $noOnlineUserList[] = $followUserInfo;
                    $result[] = [
                        'user_id' => $followUserInfo['user_id'],
                        'status' => 0,
                        'user' => $followUserInfo,
                    ];
                    $allIds[] = $followUserInfo['user_id'];
                }
            }
        }

        // 取得所有主播的封面圖與用戶資料
        // 因为 with 抓出来的资料无法更新快取
        $allAnchorInfo = $userService->getFrontcoverByIds($allIds);
        $allUserInfo = $userService->getUserInfoByIds($allIds);
        foreach ($allUserInfo as $data) {
            $allUserInfo[$data['user_id']] = $data;
        }

        // 將每一位主播的封面圖與用戶資料填入result裡
        foreach ($result as $index => $data) {
            $userId = $result[$index]['user_id'];
            $result[$index]['user'] = $allUserInfo[$userId];
            $result[$index]['anchor_info'] = ['front_cover' => $allAnchorInfo[$userId]];
        }

        // 將null轉為預設值
        foreach ($result as $index => $data) {
            if (!isset($result[$index]['leave_at']) || $result[$index]['leave_at'] == null) {
                $result[$index]['leave_at'] = '';
            }
            if (!isset($result[$index]['duration']) || $result[$index]['duration'] == null) {
                $result[$index]['duration'] = 0;
            }
            if (!isset($result[$index]['fans_after_open']) || $result[$index]['fans_after_open'] == null) {
                $result[$index]['fans_after_open'] = 0;
            }
        }
        // 移除手機號
        foreach ($result as $index => $data) {
            if (isset($result[$index]['user']['cellphone'])) {
                $result[$index]['user']['cellphone'] = '';
            }
        }

        return $result;
    }

    /**
     * 將live room資料與cache裡的熱門直播排行榜資料合併
     * 修正 热度数值
     */
    private function mergeHotLiveRommWithCache($liveRoomData, $hotLeaderboardCacheData)
    {
        if ($hotLeaderboardCacheData == []) {
            return $liveRoomData;
        }
        $userIdToHotInfo = [];
        for ($i = 0; $i < count($hotLeaderboardCacheData); $i++) {
            $userIdToHotInfo[$hotLeaderboardCacheData[$i]['user_id']] = $hotLeaderboardCacheData[$i];
        }

        for ($i = 0; $i < count($liveRoomData); $i++) {
            $liveRoomData[$i]['hot_value'] = $userIdToHotInfo[$liveRoomData[$i]['user_id']]['hot_value'];
        }

        usort($liveRoomData, function ($a, $b) {
            return $b['hot_value'] - $a['hot_value'];
        });
        return $liveRoomData;
    }

    /**
     * 批次取得所有直播室
     *
     * @param array $filter
     * @return void
     */
    public function batchGetRooms($queryValue = "", $offset = 1)
    {
        $where = [
            'status' => $this->repository->model()::STATUS_LIVE,
            'password' => '',
        ];
        $orderByColumn = 'created_at';
        $whereResult = ['game' => [],
            'hot' => [],
            'new' => [],
            'follow' => [],
        ];
        $followUserIdList = $this->userfollowRepository->skipCache(true)->findWhere(['user_id' => id()], ['follow_uid'])->pluck('follow_uid')->toArray();

        foreach ($whereResult as $queryType => $value) {
            $whereResult[$queryType] = $this->getRooms($queryType, $queryValue, $offset, $followUserIdList);
        }
        return $whereResult;
    }
    /**
     * 开启直播
     *
     * @param [type] $data
     * @return void
     */
    public function open($data)
    {
        $where = $data;
        unset($where['game_slug']);
        //检查用户是否被封锁
        if (!isset($data['password']) || !$data['password']) {
            $data['password'] = '';
        }
        $user = $this->userRepository->findWhere(['id' => $data['user_id']])->first();
        $userConfig = $this->userConfigRepository->findWhere(['user_id' => $data['user_id']])->first();

        if (!$user->is_anchor || ($userConfig && $userConfig->is_lock == $this->userConfigRepository->model()::IS_BLOCK_YES)) {
            throw new \Exception(__('liveRoom.forbidden_live'));
        }
        $where['status'] = $this->repository->model()::STATUS_LIVE;
        $list = $this->repository->findWhere($where);
        if ($list->count() > 0) {
            return $list->first();
        } else {
            $followUserNumber = $this->userfollowRepository->skipCache(true)->findWhere(['follow_uid' => $data['user_id']])->count();
            $defaultInsertData = [
                'fans_before_open' => $followUserNumber,
                'status' => $this->repository->model()::STATUS_LIVE,
                'start_at' => date("Y-m-d H:i:s", time()),
            ];
            $insertData = array_merge($defaultInsertData, $data);
            $data = $this->repository->create($insertData);
            $urls = \Live::pushPullFlow($data->id, date("Y-m-d H:i:s", strtotime("+1 day")));
            $data->stream_urls = json_encode($urls);
            $data->save();
        }
        //計算熱度
        $this->enqueueHotCalculator($data->id);
        return $data;
    }

    /**
     * 透過主播用戶ID，關房間
     *
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T20:05:23+0800
     */
    public function close($userId)
    {
        $where = [
            'user_id' => $userId,
            'status' => $this->repository->model()::STATUS_LIVE,
        ];
        $list = $this->repository->findWhere($where);
        // 取得直播間model
        $roomModel = $list->first();
        if ($roomModel == null) {
            return __('liveRoom.closeSuccess');
        }

        $this->closeRoomCallBack($roomModel);
        return __('liveRoom.closeSuccess');
    }

    /**
     * 統一在這裡做離開關房間的行為
     *
     * @param    BaseModel                $room [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T20:02:53+0800
     */
    public function closeRoomCallBack(BaseModel $room)
    {
        // 取得結束直播時的粉絲人數
        $userId = $room->user_id;
        $followUserNumber = $this->userfollowRepository->skipCache(true)->findWhere(['follow_uid' => $userId])->count();
        // 更新該直播間狀態
        $now = date("Y-m-d H:i:s", time());
        $updateData = [
            'fans_after_open' => $followUserNumber,
            'status' => $this->repository->model()::STATUS_STOP,
            'leave_at' => $now,
        ];
        $this->repository->update($updateData, $room->id);
        // 增加經驗值
        $startTime = $room->created_at->timestamp;
        $endTime = Carbon::now()->timestamp;
        app(UserLevelService::class)->addExpByLivePlay($userId, $startTime, $endTime);
        //清除相關的快取資料
        //送禮的隊列
        $redis = \Cache::store('redis')->getRedis();
        $listKey = $this->getGiftHotListCacheKey($room->id);
        $redis->del($listKey);
        //計算過的所有熱度
        $infoKey = $this->getHotInfoCacheKey($room->id);
        \Cache::forget($infoKey);

        // 更新主播個人排行榜
        \Queue::pushOn(pool('leaderboard'), new \App\Jobs\PersonalLeaderboardJob($userId));
    }

    /**
     * 更新主播個人排行榜
     */
    public function updatePersonalLeaderboardByAnchorId($userId)
    {
        $service = app(LeaderboardService::class);
        $service->makePersonalTodayByAnchorId($userId);
        $service->makePersonalMonthByAnchorId($userId);
        $service->makePersonalAllByAnchorId($userId);
    }

    /**
     * 用room id關閉直播間
     */
    public function closeByRoomId($roomId)
    {
        $where = [
            'id' => $roomId,
            'status' => $this->repository->model()::STATUS_LIVE,
        ];
        // 取得直播間model
        $roomModel = $this->repository->findWhere($where)->first();
        if ($roomModel == null) {
            return __('liveRoom.closeSuccess');
        }

        // 取得結束直播時的粉絲人數
        $userId = $roomModel->user_id;
        $followUserNumber = $this->userfollowRepository->skipCache(true)->findWhere(['follow_uid' => $userId])->count();

        // 更新該直播間狀態
        $now = date("Y-m-d H:i:s", time());
        $updateData = [
            'fans_after_open' => $followUserNumber,
            'status' => $this->repository->model()::STATUS_STOP,
            'leave_at' => $now,
        ];
        $this->repository->update($updateData, $roomModel->id);

        // 增加經驗值
        $startTime = Carbon::parse($roomModel->created_at)->timestamp;
        $endTime = Carbon::now()->timestamp;
        app(UserLevelService::class)->addExpByLivePlay($userId, $startTime, $endTime);

        // 更新主播個人排行榜
        \Queue::pushOn(pool('leaderboard'), new \App\Jobs\PersonalLeaderboardJob($userId));

        return __('liveRoom.closeSuccess');
    }

    /**
     * 用房間id取得房間資料
     */
    public function getRoomByRoomId(string $roomId)
    {
        return $this->repository->findByField('id', $roomId);
    }

    public function enter($roomId, $password)
    {
        $where = [
            'id' => $roomId,
            'password' => $password,
        ];
        $record = $this->repository->findWhere($where);

        if ($record->count() == 0) {
            throw new \Exception(__('liveRoom.notFoundLiveRoom'));
        }
        $record = $record->first();
        $streamUrl = "";
        if (count($record->stream_urls['pull']) > 0) {
            $streamUrl = $record->stream_urls['pull'][0];
        }
        $this->isCanCallEnterRoomCallback($roomId, id());
        return $streamUrl;
    }

    /**
     * 取得進入房間鎖
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T09:22:20+0800
     */
    protected function roomEnterLockKey($roomId, $userId)
    {
        return self::LOCK_ENTER_ROOM_PREFIX_KEY . $userId . '@' . $roomId;
    }

    /**
     * 取得離開房間鎖
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T09:22:20+0800
     */
    protected function roomLeaveLockKey($roomId, $userId)
    {
        return self::LOCK_LEAVE_ROOM_PREFIX_KEY . $userId . '@' . $roomId;
    }
    /**
     * 是否可以呼叫進房間CallBack
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   boolean                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T09:27:02+0800
     */
    protected function isCanCallEnterRoomCallback($roomId, $userId)
    {
        $redis = \Cache::store('redis')->getRedis();
        $lockKey = $this->roomEnterLockKey($roomId, $userId);
        $isLock = redisLock($lockKey);
        if (!$isLock) {
            return false;
        } else {
            \Queue::pushOn(config('queue.connections.' . config('queue.default') . '.queue'), new \App\Jobs\LiveRoomEnter($roomId, $userId));
            return true;
        }
    }

    /**
     * 是否可以呼叫離開房間CallBack
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   boolean                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T09:27:02+0800
     */
    protected function isCanCallLeaveRoomCallback($roomId, $userId)
    {
        $redis = \Cache::store('redis')->getRedis();
        $lockKey = $this->roomLeaveLockKey($roomId, $userId);
        $isLock = redisLock($lockKey);
        if (!$isLock) {
            return false;
        } else {
            \Queue::pushOn(config('queue.connections.' . config('queue.default') . '.queue'), new \App\Jobs\LiveRoomLeave($roomId, $userId));
            return true;
        }
    }
    /**
     * 進入房間CallBack
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T09:26:50+0800
     */
    public function enterCallback($roomId, $userId)
    {
        $lockKey = $this->roomEnterLockKey($roomId, $userId);
        releaseRedisLock($lockKey);
        $where = [
            'id' => $roomId,
        ];
        $record = $this->repository->findWhere($where);
        if ($record->count() == 0) {
            throw new \Exception(__('liveRoom.notFoundLiveRoom'));
        }
        $record = $record->first();
        $cacheKey = $this->getRoomListKey($userId);
        $preRecord = \Cache::get($cacheKey);
        if (isset($preRecord['room_id']) && ($preRecord['room_id'] == $roomId)) {
            return true;
        }
        $preRecord = [
            'room_id' => $roomId,
            'enter_at' => time(),
        ];
        \Cache::put($cacheKey, $preRecord, 24 * 3);
        $record->real_user_number = $record->real_user_number + 1;
        $record->save();
        $broadCastData = [
            'GroupId' => (string) $record->user_id,
            'MsgBody' => [
                [
                    'MsgType' => 'TIMCustomElem',
                ],
            ],
        ];
        $result = \IM::sendLiveRoomBroadcast($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.101', ['num' => $record->real_user_number + $record->robot_user_number])]]);
    }
    /**
     * 返回 房間用戶列表 Key
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T13:54:02+0800
     */
    protected function getRoomListKey($userId)
    {
        return self::ROOM_USER_ID_LIST_KEY . $userId;
    }

    /**
     * 離開房間
     *
     * @param    [type]                   $roomId [description]
     * @param    [type]                   $userId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-15T14:09:03+0800
     */
    public function leave($roomId, $userId)
    {
        $where = [
            'id' => $roomId,
        ];
        $record = $this->repository->findWhere($where);
        if ($record->count() == 0) {
            throw new \Exception(__('liveRoom.notFoundLiveRoom'));
        }
        $this->isCanCallLeaveRoomCallback($roomId, $userId);
        return __('liveRoom.leaveSuccess');
    }

    public function leaveCallBack($userId)
    {

        $cacheKey = $this->getRoomListKey($userId);
        $preRecord = \Cache::get($cacheKey); // 格式为 {room_id:房间ID, enter_at:进房时间 Timestamp}

        //如果已经离开，则 回报成功
        if (!isset($preRecord['room_id'])) {
            return true;
        }
        $roomId = $preRecord['room_id'];
        $where = [
            'id' => $roomId,
        ];
        $record = $this->repository->findWhere($where);
        $record = $record->first();
        $record->real_user_number = $record->real_user_number - 1;
        $record->save();
        // 用户经验值
        app(UserLevelService::class)->addExpByLiveWatch($userId, $preRecord['enter_at'], time());

        \Cache::forget($cacheKey);

    }

    /**
     * 多用戶離開直播間
     */
    public function leaveWithMultiUser($groupId, $userIdList)
    {
        $userId = \IM::getInnerGroupIdFromImGroupId($groupId);
        $where = [
            'user_id' => $userId,
            'status' => $this->repository->model()::STATUS_LIVE,
        ];
        $record = $this->repository->findWhere($where);

        $number = count($userIdList);
        for ($i = 0; $i < $number; $i++) {
            //实际上是个别跑  leaveCallBack
            $this->leaveCallBack($userIdList[$i]);
        }

        if ($record->count() > 0) {
            $record = $record->first();
            $record->real_user_number = $record->real_user_number - $number;
            if ($record->real_user_number < 0) {
                $record->real_user_number = 0;
            }
            $record->save();
        }
        return __('liveRoom.leaveSuccess');
    }

    /**
     * 直播結束統計資訊
     *
     * @param int $user_id
     * @param int $roomId
     *
     * @return array
     */
    public function endInfo($user_id, $roomId)
    {
        $result = [];
        $where = [
            'id' => $roomId,
        ];
        // 從db取出房間資料
        $record = $this->repository->findWhere($where);
        if ($record->count() == 0) {
            throw new \Exception(__('liveRoom.notFoundLiveRoom'));
        }
        $recordData = $record->first();

        if ($recordData->user_id != $user_id) {
            throw new \Exception(__('liveRoom.userPermissionDeny'));
        }

        $result['room_id'] = $recordData->id;
        $result['anchor_id'] = $recordData->user_id;
        $result['watch_user_number'] = $recordData->real_user_number;
        $result['total_income'] = (int) $recordData->total_receive_gold;

        $startTime = new Carbon($recordData->created_at);

        $duration = Carbon::now()->diff($startTime)->format('%H:%I:%S');
        $result['live_time'] = $duration;

        // 計算 follow 人數
        if ($recordData->fans_after_open == null) {
            $fansNumber = $this->userfollowRepository->skipCache(true)->findWhere(['follow_uid' => $user_id])->count();
            $result['new_fans'] = $fansNumber - $recordData->fans_before_open;
        } else {
            $result['new_fans'] = $recordData->fans_after_open - $recordData->fans_before_open;
        }

        // 計算遊戲收益
        $result['game_income'] = (int)($recordData->total_game_bet - $recordData->total_game_win);
        return $result;
    }

    public function getSelfRoom($roomId)
    {
        $where = [
            'id' => $roomId,
            'user_id' => id(),
        ];
        $record = $this->repository->findWhere($where);
        if ($record->count() == 0) {
            throw new \Exception(__('liveRoom.notFoundLiveRoom'));
        }
        $record = $record->first();
        $url = ($record->stream_urls['push']) ?? '';
        return $url;
    }

    /**
     * 廣播熱度與總收禮金幣數到直播間聊天群組裡
     */
    public function brocastGoldAnHotDataToLiveRoom($groupId, $receiveGold, $hotValue)
    {
        $data = [
            'gold' => $receiveGold, 'hot_value' => $hotValue,
        ];

        $jsonData = '';
        if ($data != '') {
            $jsonData = json_encode($data);
        }

        $broadCastData = [
            'GroupId' => (string) $groupId,
            'MsgBody' => [
                [
                    'MsgType' => 'TIMCustomElem',
                ],
            ],
        ];
        $result = \IM::sendLiveRoomBroadcast($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.103', ['hotData' => $jsonData])]]);
    }

    /**
     * 加入熱度計算隊列
     *
     * @param    BaseModel                $model [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T11:17:17+0800
     */
    public function enqueueHotCalculator($roomId, $currentTimes = 0)
    {
        //每十秒做一次
        \Queue::laterOn('hot_calculator', self::HOT_CALCULATOR_DELAY_TIME, new \App\Jobs\HotValueCalculator($roomId, $currentTimes));
    }
    /**
     * 真正要計算熱度的地方
     *
     * @param    BaseModel                $model  [description]
     * @param    int                   $currentTimes [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T11:33:04+0800
     */
    public function doHotValueCalculator($roomId, $currentTimes)
    {
        try {
            $room = $this->repository->find($roomId);
            if ($room->status != $this->repository->model()::STATUS_LIVE) {
                //如果是關閉直播，就不用計算
                $this->setHotCalculatorQueueStatus($room->id, $currentTimes + 1, false);
                return true;
            }
            //去访问直播，确认房间状态
            $room = $this->manualCloseLiveRoom($room);
            if ($room->status != $this->repository->model()::STATUS_LIVE) {
                //如果是關閉直播，就不用計算
                $this->setHotCalculatorQueueStatus($room->id, $currentTimes + 1, false);
                return true;
            }
        } catch (\Exception $ex) {
            //找不到房間，單純寫入log記錄，不丟例外，避免讓系統一直重做這筆
            wl($ex);
            return false;
        }
        $addGrades = [];
        $configs = getBaseHotConfig();
        //當前時間要依照房間建立時間開始算
        $now = $room->created_at->timestamp + $currentTimes * 10;
        foreach ($configs as $key => $config) {
            //是否需要計算差異
            $isDiff = true;
            if ($config['type'] == BaseHotConfigure::TYPE_FIX) {
                $isDiff = false;
            }
            $addGrades[] = $this->doHotValueCalculatorByConfig($room, $config, $isDiff, $now);
        }
        $sum = collect($addGrades)->sum();
        //取得道具增加的熱度
        $giftHotValue = $this->getAllGiftHotValue($roomId);
        $infoKey = $this->getHotInfoCacheKey($room->id);
        $totalHotValue = $sum + $giftHotValue;
        \Cache::forever($infoKey, $totalHotValue);
        $this->brocastHotDataToLiveRoom($room->user_id, $totalHotValue);
        //遞回加入排程
        $this->setHotCalculatorQueueStatus($room->id, $currentTimes + 1);
        $this->enqueueHotCalculator($room->id, $currentTimes + 1);

    }

    /**
     * 廣播熱度與總收禮金幣數到直播間聊天群組裡
     */
    public function brocastHotDataToLiveRoom($groupId, $hotValue)
    {
        $data = [
            'hot_value' => $hotValue,
        ];

        $jsonData = '';
        if ($data != '') {
            $jsonData = json_encode($data);
        }

        $broadCastData = [
            'GroupId' => (string) $groupId,
            'MsgBody' => [
                [
                    'MsgType' => 'TIMCustomElem',
                ],
            ],
        ];
        $result = \IM::sendLiveRoomBroadcast($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.' . app('im')::MESSAGE_TYPE_107, ['hotData' => $jsonData])]]);
    }

    /**
     * 計算動態的熱度(有差異的)
     *
     * @param    [type]                   $room   [description]
     * @param    [type]                   $config [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T14:56:32+0800
     */
    protected function doHotValueCalculatorByConfig($room, $config, $isDiff = true, $now)
    {
        $range = $config['range'];
        $relation = $config['relation'];
        $configId = $config['id'];
        $config['range'] = $config['range'];
        $column = $config['metric_column'];
        //檢查的指標當前數值
        $metricValue = 0;
        //取得正在跑的獎勵
        $runningReward = BaseHotConfigure::getRunningReward($room->id, $configId); //如果過期的話，
        if ($runningReward) {
            $maxTime = $runningReward['addTime'];
        } else {
            $maxTime = self::HOT_CALCULATOR_DELAY_TIME;
        }
        $minMetric = $this->getMinMetricByDuration($room, $configId, $now, $maxTime);
        $minMetricTime = $minMetric['time'] ?? 0;
        if ($relation == BaseHotConfigure::RELATION_SELF) {
            //如果當前規則要檢查的 關聯 是 self 的話，就是找當前記錄
            $metricValue = $room->$column;
        } else {
            //否則就取關聯的 指標欄位加總
            $room->load([$relation => function ($query) use ($minMetricTime) {
                $query->where('created_at', '>', date("Y-m-d H:i:s", $minMetricTime));
            }]);
            $metricValue = $room->$relation->sum($config['metric_column']);
            if (is_null($metricValue)) {
                $metricValue = 0;
            }
        }
        //把當前數值記錄下來，供下次使用
        /**
         * 如果是 計算差異的話，邏輯是 先把 當前時間的 指標數值，抄寫一份到 redis
         * 然後，如果發生有取到獎勵的事件的話,且獎勵沒有變更把當前的時間，設為最近的檢查點
         */
        BaseHotConfigure::setMetricValue($room->id, $configId, $metricValue, $now);
        if ($isDiff) {
            //取得上次檢查點的數值
            $checkPointMetricValue = BaseHotConfigure::getTheLastestCheckPointMetricValue($room->id, $configId);
            $metricValueDiff = $metricValue - $checkPointMetricValue;
        } else {
            $metricValueDiff = $metricValue;
        }
        $metricValueDiff = ($metricValueDiff >= 0) ? $metricValueDiff : 0;
        $reward = $this->getFallPointReward($metricValueDiff, $config['range']);

        if ($reward) {
            $calCurrentValue = $reward['addGrade'];
            if ($isDiff) {
                if (!$runningReward) {
                    //如果當前沒有正在跑的獎勵
                    //要把當前的獎勵熱度, 發生的時候前十分鐘最少的數據寫進去
                    $minMetricValue = ($minMetric['min']) ?? 0;
                    BaseHotConfigure::setTheLastestCheckPointMetric($room->id, $configId, $minMetricValue);
                }
                if (!$runningReward || ($calCurrentValue > $runningReward['addGrade'])) {
                    //如果為空的話, 或是當前的獎勵 addGrade 大於 正在跑的，就刷新
                    BaseHotConfigure::setRunningReward($room->id, $configId, $reward);
                }
                //如果當前的獎勵沒有比較好的話，就用正在跑的獎勵
                if ($runningReward) {
                    if ($calCurrentValue <= $runningReward['addGrade']) {
                        $calCurrentValue = $runningReward['addGrade'];
                    }
                }
            }
        } else {
            if ($runningReward) {
                $calCurrentValue = $runningReward['addGrade'];
            } else {
                $calCurrentValue = 0;
            }
        }
        //計算 該返回多少熱度
        $metricAddCount = BaseHotConfigure::getMetricValueByUnitType($metricValueDiff, $config);
        return $metricAddCount * $calCurrentValue;
    }

    /**
     * 取得最近十分鐘最低的指標值
     *
     * @param    [type]                   $room [description]
     * @param    [type]                   $configId [description]
     * @param    [type]                   $now      [description]
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T17:53:51+0800
     */
    private function getMinMetricByDuration($room, $configId, $now, $maxTime)
    {
        $metrics = [];
        //指標時間
        $metricKeys = [];
        $maxIterationCount = 60 * $maxTime / self::HOT_CALCULATOR_DELAY_TIME;
        $nowTimes = ($now - $room->created_at->timestamp) / self::HOT_CALCULATOR_DELAY_TIME;
        $maxIterationCount = (($nowTimes - $maxIterationCount) < 60) ? 0 : $nowTimes - $maxIterationCount;
        for ($i = $nowTimes; $i > $maxIterationCount; $i--) {
            $time = $room->created_at->timestamp + $i * self::HOT_CALCULATOR_DELAY_TIME;

            $metricKeys[] = BaseHotConfigure::getMetricValueCacheKey($room->id, $configId, $time);
            // $key = BaseHotConfigure::getMetricValueCacheKey($room->id, $configId, $time);
            // $metrics[$key] = \Cache::get(BaseHotConfigure::getMetricValueCacheKey($room->id, $configId, $time));
        }
        if ($metricKeys) {
            $metrics = \Cache::many($metricKeys);
        }
        if (!$metrics) {
            return [];
        } else {
            $all = collect($metrics)->filter(function ($item) {
                //要把null 的排除，不然会误判
                return !is_null($item);
            })->sort()->all();
            return ['time' => $this->getTimeFromCacheKey(key($all)), 'min' => current($all)];
        }
    }
    public function getTimeFromCacheKey($key)
    {
        $tmp = explode('@', $key);
        if ($tmp) {
            $time = end($tmp);
            if (!is_int($time)) {
                return 0;
            } else {
                return $time;
            }
        } else {
            return 0;
        }
        return 0;
    }
    /**
     * 取得落點所得到的熱度數值
     *
     * @param    [type]                   $currentValue [description]
     * @param    [type]                   $range        [description]
     *
     * @return   [type]                                 [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T11:55:52+0800
     */
    private function getFallPointReward($currentValue, $range)
    {
        if (!$range) {
            return [];
        }
        $match = collect($range)->filter(function ($item) use ($currentValue) {
            if ($currentValue >= $item['min'] && $currentValue <= $item['max']) {
                return true;
            } else {
                return false;
            }
        })->first();
        return $match['reward'] ?? [];
    }
    /**
     * 將禮物的熱度加入redis list 結構去
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T19:51:11+0800
     */
    public function pushGiftToHotList($roomId, $giftOrder)
    {
        //只加入可以增加熱度的
        if (isset($giftOrder->hot_value) && $giftOrder->hot_value > 0) {
            $cacheKey = $this->getGiftHotListCacheKey($roomId);
            $redis = \Cache::store('redis')->getRedis();
            //只取 hot_value ，跟過期時間
            $data = $giftOrder->only(['hot_value', 'hot_expired_time']);
            $redis->hset($cacheKey, $giftOrder->id, json_encode($data));
        }
    }
    /**
     * 取得所有送禮的熱度，如果有過期的，也會一併刪除
     *
     * @param    int                   $roomId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T20:44:56+0800
     */
    public function getAllGiftHotValue($roomId)
    {
        $cacheKey = $this->getGiftHotListCacheKey($roomId);
        $redis = \Cache::store('redis')->getRedis();
        $gifts = $redis->hgetall($cacheKey);
        $now = time();
        $totalHotValues = 0;
        $delKeys = [];
        foreach ($gifts as $key => $item) {
            //把過期的清掉
            $content = json_decode($item, true);
            try {
                if (is_array($content['hot_expired_time'])) {
                    $hot_expired_time = $content['hot_expired_time']['date'];
                } else {
                    $hot_expired_time = $content['hot_expired_time'];
                }
                $expiredTime = strtotime($hot_expired_time);
            } catch (\Exception $ex) {
                wl($ex);
                wl($content);
                continue;
            }

            if ($now > $expiredTime) {
                $delKeys[] = $key;
            }
            $totalHotValues += $content['hot_value'];
        }
        if ($delKeys) {
            $redis->hdel($cacheKey, $delKeys);
        }
        return $totalHotValues;
    }
    /**
     * 取得禮物熱度快取鍵值
     *
     * @param    [type]                   $roomId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T19:52:53+0800
     */
    public static function getGiftHotListCacheKey($roomId)
    {
        return config('cache.prefix') . self::GIFT_HOT_LIST_CACHE_KEY_PREFIX . '@' . $roomId;
    }

    /**
     * 返回熱度計算過的數值鍵值
     *
     * @param    [type]                   $roomId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-29T20:12:04+0800
     */
    public static function getHotInfoCacheKey($roomId)
    {
        return self::HOT_INFO_CACHE_KEY . ':' . $roomId;
    }
    /**
     * 透過 userId 返回 當前進入的房間 資訊 ，只返回 房間ID 跟進房時間
     *
     * @param    int                   $userId 用戶ID
     *
     * @return   array                            [
    'room_id' => $roomId,
    'enter_at' => time(),
    ];
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-30T09:11:07+0800
     */
    public function getCurrentRoomRecordByUserId($userId)
    {
        $cacheKey = $this->getRoomListKey($userId);
        $preRecord = \Cache::get($cacheKey); //
        return $preRecord;
    }

    /**
     * 设定热度计算队列Queue 存活队列
     *
     * @param    [type]                   $roomId [description]
     * @param    integer                  $times  [description]
     * @param    boolean                  $status [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-04T14:20:25+0800
     */
    public function setHotCalculatorQueueStatus($roomId, $times = 0, $status = true)
    {
        $cacheKey = $this->getHotCalculatorQueueCacheKey($roomId);
        if ($status) {
            \Cache::put($cacheKey, $times, 1);
        } else {
            \Cache::forget($cacheKey);
        }
    }

    /**
     * 取得热度排队
     *
     * @param    [type]                   $roomId [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-04T14:47:22+0800
     */
    public function getHotCalculatorQueueCacheKey($roomId)
    {
        return self::HOT_CALCULATOR_QUEUE_CACHE . '@' . $roomId;
    }
    /**
     * 重启未重算热度的
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-04T14:46:54+0800
     */
    public function restartHotQueue()
    {
        //取得所有正在执行的直播房
        $keyPrefix = self::HOT_CALCULATOR_QUEUE_CACHE . '@';
        $cacheKeys = $this->repository->findWhere(['status' => $this->repository->model()::STATUS_LIVE], ['id'])
            ->map(function ($item) {
                $item->cacheKey = $this->getHotCalculatorQueueCacheKey($item->id);
                return $item;
            })
            ->pluck('cacheKey')->toArray();
        if ($cacheKeys) {
            $caches = \Cache::many($cacheKeys);
            foreach ($caches as $key => $value) {
                if (!$value) {
                    $keyParts = explode('@', $key);
                    $roomId = end($keyParts);
                    $this->enqueueHotCalculator($roomId);
                }
            }
        }
    }

    /**
     * 更新直播房状态
     *
     * @return   [type]                   [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-20T09:20:55+0800
     */
    public function refreshLiveRoomStatus()
    {
        //先去找正在直播的
        $enableStatus = $this->repository->model()::STATUS_LIVE;
        $disAbleStatus = $this->repository->model()::STATUS_STOP;
        $where = [
            'status' => $enableStatus,
            // 'password' => '',
        ];
        $roomList = $this->repository->findWhere($where)->map(function ($room) use ($disAbleStatus) {
            $parameters = [
                'stream_name' => $room->id,
            ];
            try {
                $resultJson = \Live::query($parameters);
                $result = json_decode($resultJson, true);
                if (json_last_error()) {
                    wl('Live Room ' . $room->id . ' Query Error, result =' . $resultJson);
                }
                if ($result['StreamState'] == \Live::getInActiveString()) {
                    $this->closeRoomCallBack($room);
                }
            } catch (\Exception $ex) {
                wl($ex);
            }
        });
    }

    /**
     * 手动关闭没在直播的房间
     *
     * @param    [type]                   $room [description]
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-20T09:46:07+0800
     */
    public function manualCloseLiveRoom($room)
    {
        $parameters = [
            'stream_name' => $room->id,
        ];
        try {
            $resultJson = \Live::query($parameters);
            $result = json_decode($resultJson, true);
            if (json_last_error()) {
                wl('Live Room ' . $room->id . ' Query Error, result =' . $resultJson);
            }
            if ($result['StreamState'] == \Live::getInActiveString()) {
                $this->closeRoomCallBack($room);
            }
        } catch (\Exception $ex) {
            wl($ex);
        }
        return $room;
    }

    /**
     * 取得单一房间内容
     *
     * @param    [type]                   $roomId   [description]
     * @param    [type]                   $password [description]
     *
     * @return   [type]                             [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-20T14:01:26+0800
     */
    public function info($roomId)
    {
        $roomList = $this->getRooms(self::ROOM_QUERY_TYPE_QUERY_ID, $roomId);
        return array_pop($roomList);
    }
}
