<?php

namespace App\Services;

use App\Models\LiveRoom as LiveRoomModel;
use App\Repositories\Interfaces\AnalyticAnchorReceiveGoldStatisticRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;
use App\Repositories\Interfaces\HotAnchorRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Services\LiveRoom;
use App\Services\UserService;

use \Carbon\Carbon;

//排行榜服务
class LeaderboardService
{
    const LEADERBOARD_MIN_USER_NUMBER = 5;

    const LEADERBOARD_MAX_USER_NUMBER = 100;

    const LEADERBOARD_USER_NUMBER = 100;

    const LEADERBOARD_CACHE_KEY = 'leaderboard';

    const LEADERBOARD_PERSONAL_CACHE_KEY = 'personal';

    const LEADERBOARD_FANS_CACHE_KEY = 'fans';

    const LEADERBOARD_ANCHOR_CACHE_KEY = 'anchor';

    const LEADERBOARD_FANS_LiVE_ROOM_CACHE_KEY = 'live_room:fans';

    const LEADERBOARD_HOT_LiVE_ROOM_CACHE_KEY = 'live_room:hot';

    const LEADERBOARD_TODAY_DAY_CACHE_KEY = 'total:day';

    const LEADERBOARD_TODAY_WEEK_CACHE_KEY = 'total:week';

    const LEADERBOARD_TODAY_MONTH_CACHE_KEY = 'total:month';

    const LEADERBOARD_TODAY_ALL_CACHE_KEY = 'total:all';

    private $userRepository;

    private $liveRoomRepository;

    private $baseGiftTypeRepository;

    private $giftTransactionOrderRepository;

    private $analyticAnchorReceiveGoldStatisticRepository;

    public function __construct(
        UserRepository $userRepository,
        LiveRoomRepository $liveRoomRepository,
        HotAnchorRepository $hotAnchorRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        GiftTransactionOrderRepository $giftTransactionOrderRepository,
        AnalyticAnchorReceiveGoldStatisticRepository $analyticAnchorReceiveGoldStatisticRepository) {
        $this->userRepository = $userRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->hotAnchorRepository = $hotAnchorRepository;
        $this->baseGiftTypeRepository = $baseGiftTypeRepository;
        $this->giftTransactionOrderRepository = $giftTransactionOrderRepository;
        $this->analyticAnchorReceiveGoldStatisticRepository = $analyticAnchorReceiveGoldStatisticRepository;
    }

    /**
     * 取得所有total排行榜資料
     */
    public function getAllTotalLeaderboardData()
    {
        $keys = $this->getTotalKey();

        $anchorDay = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['day']));
        $anchorWeek = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['week']));
        $anchorMonth = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['month']));
        $anchorAll = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['all']));
        $fansDay = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['day']));
        $fansWeek = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['week']));
        $fansMonth = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['month']));
        $fansAll = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['all']));

        return [
            'anchor' => [
                'day' => $anchorDay,
                'week' => $anchorWeek,
                'month' => $anchorMonth,
                'all' => $anchorAll,
            ],
            'fans' => [
                'day' => $fansDay,
                'week' => $fansWeek,
                'month' => $fansMonth,
                'all' => $fansAll,
            ],
        ];
    }

    /** 
     * 依照類型與日期區間取得排行榜資料
     */
    public function getLeaderboardDataByTypeAndDateRange($type, $range)
    {
        $keys = $this->getTotalKey();
        $result = [];
        if ($type == 'anchor') {
            switch($range) {
                case 'day':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['day']));
                break;
                case 'week':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['week']));
                break;
                case 'month':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['month']));
                break;
                case 'all':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['anchor']['all']));
                break;
            }
        }

        if ($type == 'fans') {
            switch($range) {
                case 'day':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['day']));
                break;
                case 'week':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['week']));
                break;
                case 'month':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['month']));
                break;
                case 'all':
                $result = $this->refreshUserDataFromCache(\Cache::get($keys['fans']['all']));
                break;
            }
        }

        return $result;
    }
    
    /**
     * 用快取裡的資料更新排行榜的用戶資料
     */
    public function refreshUserDataFromCache($allLeaderboardData)
    {
        if ($allLeaderboardData == null || $allLeaderboardData == []){
            return [];
        }
        $length = count($allLeaderboardData);
        $userService =  app(UserService::class);
        
        // 取得排榜上的所有用戶id
        $allIds = [];
        for ($i=0; $i<$length; $i++) {
            $allIds[] = $allLeaderboardData[$i]['user_id'];
        }

        // 依照用戶id取得資料
        $allUserData = $userService->getUserInfoByIds($allIds);
        $allUserDataWithKey = [];
        $length = count($allUserData);
        for ($i=0; $i<$length; $i++) {
            $userData = $allUserData[$i];
            $userId = $userData['user_id'];
            $allUserDataWithKey[$userId] = $userData;
        }

        // 將用戶資料寫回排行榜資料
        $length = count($allLeaderboardData);
        for ($i=0; $i<$length; $i++) {
            $userId = $allLeaderboardData[$i]['user_id'];

            $userData = $allUserDataWithKey[$userId];

            $allLeaderboardData[$i]['pretty_id'] = (int)$userData['pretty_id'];
            $allLeaderboardData[$i]['avatar'] = $userData['avatar'];
            $allLeaderboardData[$i]['level'] = (int)$userData['level'];
            $allLeaderboardData[$i]['nick_name'] = $userData['nick_name'];
            $allLeaderboardData[$i]['sex'] = (int)$userData['sex'];
        }
        return $allLeaderboardData;
    }

    /**
     * 取得指定用戶的排行榜資料
     */
    public function getPersonalLeaderboardData($userId)
    {
        $day = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalDayKey($userId)));
        $week = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalWeekKey($userId)));
        $month = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalMonthKey($userId)));
        $all = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalAllKey($userId)));

        return [
            'day' => $day,
            'week' => $week,
            'month' => $month,
            'all' => $all,
        ];
    }

    /** 
     * 依照日期區間取得指定用戶的排行榜資料
     */
    public function getPersonalLeaderboardDataByDateRange($userId, $range)
    {
        $keys = $this->getTotalKey();
        $result = [];
        switch($range) {
            case 'day':
            $result = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalDayKey($userId)));
            break;
            case 'week':
            $result = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalWeekKey($userId)));
            break;
            case 'month':
            $result = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalMonthKey($userId)));
            break;
            case 'all':
            $result = $this->refreshUserDataFromCache(\Cache::get($this->getPersonalAllKey($userId)));
            break;
        }

        return $result;
    }

    /**
     * 取得直播間排行榜
     */
    public function getLiveRoomLeaderboardData($anchorId, $roomId, $number)
    {
        $result = $this->getgetLiveRoomLeaderboardDataWithNumber($anchorId, $roomId, $number);
        return $result;
    }

    /**
     * 取得前5名排行榜資訊
     */
    public function getLiveRoomTopFiveLeaderboardData($anchorId, $roomId)
    {
        $result = $this->getgetLiveRoomLeaderboardDataWithNumber($anchorId, $roomId, self::LEADERBOARD_MIN_USER_NUMBER);
        return $result;
    }

    /**
     * 取得熱門直播間的排行榜
     */
    public function getHotLiveRoomLeaderboardData()
    {
        $redisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_HOT_LiVE_ROOM_CACHE_KEY;
        $data = \Cache::get($redisKey);

        return array_slice($data, 0, self::LEADERBOARD_USER_NUMBER);
    }

    /**
     * 取得熱門直播間排行榜與指定的直播間排名資料
     */
    public function getHotLiveRoomLeaderboardAndTargetRoomRankData($roomId)
    {
        $redisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_HOT_LiVE_ROOM_CACHE_KEY;
        $allData = \Cache::get($redisKey);
        if ($allData == null || $allData == []) {
            throw new \Exception(__('response.'.Code::BAD_REQUEST), Code::BAD_REQUEST);
        }
        $targetData = [];
        $length = count($allData);
        for ($i=0; $i<$length; $i++) {
            if($allData[$i]['room_id'] == $roomId) {

                $targetData = $allData[$i];
                $targetData['rank'] = $i + 1;
                break;
            }
        }
        $result = [];
        $result['target'] = $targetData;
        $result['leaderboard'] = array_slice($allData, 0, self::LEADERBOARD_USER_NUMBER);

        return $result;
    }

    /**
     * 取得total排行榜的 redis key
     */
    public function getTotalKey()
    {
        return [
            'anchor' => [
                'day' => $this->getAnchorDayKey(),
                'week' => $this->getAnchorWeekKey(),
                'month' => $this->getAnchorMonthKey(),
                'all' => $this->getAnchorAllKey(),
            ],
            'fans' => [
                'day' => $this->getFansDayKey(),
                'week' => $this->getFansWeekKey(),
                'month' => $this->getFansMonthKey(),
                'all' => $this->getFansAllKey(),
            ],
        ];
    }

    /**
     * 取得個人的粉絲日榜的redis key
     */
    public function getPersonalDayKey($userId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_DAY_CACHE_KEY . ':' . $userId;
    }

    /**
     * 取得個人的粉絲週榜的redis key
     */
    public function getPersonalWeekKey($userId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_WEEK_CACHE_KEY . ':' . $userId;
    }

    /**
     * 取得個人的粉絲月榜的redis key
     */
    public function getPersonalMonthKey($userId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_MONTH_CACHE_KEY . ':' . $userId;
    }

    /**
     * 取得個人的粉絲總榜的redis key
     */
    public function getPersonalAllKey($userId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_ALL_CACHE_KEY . ':' . $userId;
    }

    /**
     * 取得主播日榜的redis key
     */
    public function getAnchorDayKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_DAY_CACHE_KEY;
    }

    /**
     * 取得主播週榜的redis key
     */
    public function getAnchorWeekKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_WEEK_CACHE_KEY;
    }

    /**
     * 取得主播月榜的redis key
     */
    public function getAnchorMonthKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_MONTH_CACHE_KEY;
    }

    /**
     * 取得主播總榜的redis key
     */
    public function getAnchorAllKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_ALL_CACHE_KEY;
    }

    /**
     * 取得粉絲日榜的redis key
     */
    public function getFansDayKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_DAY_CACHE_KEY;
    }

    /**
     * 取得粉絲週榜的redis key
     */
    public function getFansWeekKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_WEEK_CACHE_KEY;
    }

    /**
     * 取得粉絲月榜的redis key
     */
    public function getFansMonthKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_MONTH_CACHE_KEY;
    }

    /**
     * 取得粉絲總榜的redis key
     */
    public function getFansAllKey()
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY . ':' . self::LEADERBOARD_TODAY_ALL_CACHE_KEY;
    }

    /**
     * 取得直播間內用戶禮物消費的redis key
     */
    public function getUserInRoomKey($anchorId, $roomId, $userId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_LiVE_ROOM_CACHE_KEY . ':' . $anchorId . ':' . $roomId . ':' . $userId;
    }

    /**
     * 取得直播間裡所有用戶id的redis key
     */
    public function getAllUserIdInRoomKey($anchorId, $roomId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_LiVE_ROOM_CACHE_KEY . ':' . $anchorId . ':' . $roomId . ':' . 'all';
    }

    /**
     * 取得直播間禮物消費的redis prefix key
     */
    public function getLiveRoomPrefixKey($anchorId, $roomId)
    {
        return self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_LiVE_ROOM_CACHE_KEY . ':' . $anchorId . ':' . $roomId;
    }

    /**
     * 計算熱門直播間排行榜
     */
    public function makeHotLiveRoomLeaderBoard()
    {
        // 取出所有正在直播的直播間
        $allLiveRoomModelArray = $this->liveRoomRepository->findWhere(
            [
                'status' => LiveRoomModel::STATUS_LIVE,
            ]
        )->all();

        // 取出所有主播id和直播間id
        $allAnchorIdList = [];
        $allRoomIdList = [];
        for ($i = 0; $i < count($allLiveRoomModelArray); $i++) {
            $allRoomIdList[] = $allLiveRoomModelArray[$i]->id;
            $allAnchorIdList[] = $allLiveRoomModelArray[$i]->user_id;
        }

        // 取出所有具有hot value的送禮交易資料
        $allOrderModelArray = $this->giftTransactionOrderRepository->scopeQuery(function ($query) use ($allRoomIdList) {
            return $query->where([
                ['hot_value', '>', 0],
                ['hot_expired_time', '>', Carbon::now()],
            ])->whereIn('room_id', $allRoomIdList);
        })->get()->all();

        // 依據送禮交易資料, 統計直播間的熱度
        $roomIdToHotInfo = [];
        for ($i = 0; $i < count($allOrderModelArray); $i++) {
            $orderModel = $allOrderModelArray[$i];

            if (isset($roomIdToHotInfo[$orderModel->room_id]) == false) {
                $roomIdToHotInfo[$orderModel->room_id] = [
                    'room_id' => $orderModel->room_id,
                    'user_id' => $orderModel->receive_uid,
                    'hot_value' => $orderModel->hot_value,
                ];
            } else {
                $roomIdToHotInfo[$orderModel->room_id]['hot_value'] += $orderModel->hot_value;
            }
        }

        // 統計房間內的熱度
        for ($i = 0; $i < count($allLiveRoomModelArray); $i++) {
            $liveRoomModel = $allLiveRoomModelArray[$i];

            if (isset($roomIdToHotInfo[$liveRoomModel->id]) == false) {

                $roomIdToHotInfo[$liveRoomModel->id] = [
                    'room_id' => $liveRoomModel->id,
                    'user_id' => $liveRoomModel->user_id,
                    'hot_value' => $liveRoomModel->hot_value,
                ];

            } else {
                $roomIdToHotInfo[$liveRoomModel->id]['hot_value'] += $liveRoomModel->hot_value;
            }
        }

        // 找出最大的熱度值
        $maxHotValue = 0;
        foreach ($roomIdToHotInfo as $hotInfo) {
            if ($hotInfo['hot_value'] > $maxHotValue) {
                $maxHotValue = $hotInfo['hot_value'];
            }
        }

        // 取出所有在後台設置為熱門主播的主播id
        $allHotAnchorModelArray = $this->hotAnchorRepository->all(['user_id', 'weight'])->keyBy('user_id')->toArray();
        $hotAnchorUserIdList = array_keys($allHotAnchorModelArray);

        // 取出所有在後台設置為熱門主播的直播間資料
        $allHotAnchorRoomModelArray = $this->liveRoomRepository->scopeQuery(function ($query) use ($hotAnchorUserIdList) {
            return $query->where([
                'status' => LiveRoomModel::STATUS_LIVE,
                'password' => '',
            ])->whereIn('user_id', $hotAnchorUserIdList);
        })->get()->all();

        // 將有設置熱門主播的熱度調整為最高
        for ($i = count($allHotAnchorRoomModelArray) - 1; $i >= 0; $i--) {
            $hotAnchorRoomModel = $allHotAnchorRoomModelArray[$i];
            $maxHotValue += rand(1000, 5000);

            $roomIdToHotInfo[$hotAnchorRoomModel->id] = [
                'room_id' => $hotAnchorRoomModel->id,
                'user_id' => $hotAnchorRoomModel->user_id,
                'hot_value' => $maxHotValue,
            ];
        }

        // 將每一直播間的熱度資料寫入cache
        $keyToHotValue = [];
        foreach ($roomIdToHotInfo as $hotInfo) {
            $key = LiveRoom::HOT_INFO_CACHE_KEY . ":" . $hotInfo['room_id'];
            $keyToHotValue[$key] = $hotInfo['hot_value'];
        }
        if ($keyToHotValue) {
            // \Cache::putMany($keyToHotValue, 180);
        }

        $userIdList = [];
        foreach ($roomIdToHotInfo as $info) {
            $userIdList[] = $info['user_id'];
        }
        // 取得所有熱門主播的基本資料
        $allUserModelArray = $this->userRepository->findWhereIn('id', $userIdList)->keyBy('id')->all();
        // 將基本資料添加至排行榜資訊裡
        foreach ($roomIdToHotInfo as $roomId => $info) {
            $roomIdToHotInfo[$roomId]['pretty_id'] = $allUserModelArray[$info['user_id']]->pretty_id;
            $roomIdToHotInfo[$roomId]['avatar'] = $allUserModelArray[$info['user_id']]->avatar;
            $roomIdToHotInfo[$roomId]['level'] = $allUserModelArray[$info['user_id']]->current_level;
            $roomIdToHotInfo[$roomId]['nick_name'] = $allUserModelArray[$info['user_id']]->nickname;
            $roomIdToHotInfo[$roomId]['sex'] = $allUserModelArray[$info['user_id']]->sex;
        }

        // 排序 越大越前面
        usort($roomIdToHotInfo, function ($a, $b) {
            return $b['hot_value'] - $a['hot_value'];
        });
        $result = array_slice($roomIdToHotInfo, 0, count($roomIdToHotInfo));

        $redisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_HOT_LiVE_ROOM_CACHE_KEY;

        // 取得原有的排行榜資料
        $oldLeaderboardData = \Cache::get($redisKey);
        if ($oldLeaderboardData == null) {
            $oldLeaderboardData = [];
        }

        // 直播間熱度排行榜寫入redis
        \Cache::forever($redisKey, $result);

        // 原有的房間id=>排名
        $oldRoomIdToRank = [];
        // 原有的房間id=>用戶id
        $oldRoomIdToUserId = [];
        for ($i = 0; $i < count($oldLeaderboardData); $i++) {
            $oldRoomIdToRank[$oldLeaderboardData[$i]['room_id']] = $i + 1;
            $oldRoomIdToUserId[$oldLeaderboardData[$i]['room_id']] = $oldLeaderboardData[$i]['user_id'];
        }

        // 新的房間id=>排名
        $newRoomIdToRank = [];
        for ($i = 0; $i < count($result); $i++) {
            $newRoomIdToRank[$result[$i]['room_id']] = $i + 1;
        }
        // 通知不在新排行榜上的主播群, 讓前端不要顯示當前直播排行特效
        foreach ($oldRoomIdToRank as $roomId => $rank) {
            if (isset($newRoomIdToRank[$roomId]) == false) {
                $data = [
                    'in_rank' => false,
                    'user_id' => $oldRoomIdToUserId[$roomId],
                    'room_id' => $roomId,
                    'rank' => '',
                    'old_rank' => '',
                    'hot_value' => '',
                    'prev_rank_hot_value' => '',
                    'next_rank_hot_value' => '',
                ];
                $this->brocastLeaderboardDataToLiveRoomGroup($oldRoomIdToUserId[$roomId], \App\Services\IM\IMManager::MESSAGE_TYPE_105, $data);
            }
        }

        // 通知新排行榜上的主播群組
        for ($i = 0; $i < count($result); $i++) {
            $oldRank = -1;
            if (isset($oldRoomIdToRank[$result[$i]['room_id']])) {
                $oldRank = $oldRoomIdToRank[$result[$i]['room_id']];
            }

            $rank = $i + 1;

            $prevIndex = $i - 1;
            if ($prevIndex < 0) {
                $prevIndex = 0;
            }
            $nextIndex = $i + 1;
            if ($nextIndex >= count($result)) {
                $nextIndex = count($result) - 1;
            }

            // 通知前端更新資訊
            $data = [
                'in_rank' => true,
                'user_id' => $result[$i]['user_id'],
                'room_id' => $result[$i]['room_id'],
                'rank' => $rank,
                'old_rank' => $oldRank,
                'hot_value' => $result[$i]['hot_value'],
                'prev_rank_hot_value' => $result[$prevIndex]['hot_value'],
                'next_rank_hot_value' => $result[$nextIndex]['hot_value'],
            ];
            $this->brocastLeaderboardDataToLiveRoomGroup($result[$i]['user_id'], \App\Services\IM\IMManager::MESSAGE_TYPE_105, $data);
        }
    }

    /**
     * 個人排行榜 - 日榜
     */
    public function makePersonalDay()
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = $startDate;

        $this->makePersonalByDate(self::LEADERBOARD_TODAY_DAY_CACHE_KEY, $startDate, $endDate);
    }

    /**
     * 個人排行榜 - 週榜
     */
    public function makePersonalWeek()
    {
        $now = Carbon::now();
        $startDate = $now->startOfWeek()->format('Y-m-d');
        $endDate = $now->endOfWeek()->format('Y-m-d');

        $this->makePersonalByDate(self::LEADERBOARD_TODAY_WEEK_CACHE_KEY, $startDate, $endDate);
    }

    /**
     * 個人排行榜 - 月榜
     */
    public function makePersonalMonth()
    {
        $now = Carbon::now();
        $startDate = $now->startOfMonth()->format('Y-m-d');
        $endDate = $now->endOfMonth()->format('Y-m-d');

        $this->makePersonalByDate(self::LEADERBOARD_TODAY_MONTH_CACHE_KEY, $startDate, $endDate);
    }

    /**
     * 個人排行榜 - 總榜
     */
    public function makePersonalAll()
    {
        $modelArray = $this->analyticAnchorReceiveGoldStatisticRepository->all()->all();
        $this->makePersonalAllByModelArray($modelArray);
    }

    /**
     * 所有用戶的排行榜 - 日榜
     */
    public function makeTotalDay()
    {
        $this->makeTotalDataByDate(self::LEADERBOARD_TODAY_DAY_CACHE_KEY);
    }

    /**
     * 所有用戶的排行榜 - 週榜
     */
    public function makeTotalWeek()
    {
        $now = Carbon::now();
        $startDate = $now->startOfWeek()->format('Y-m-d');
        $endDate = $now->endOfWeek()->format('Y-m-d');

        $this->makeTotalDataByDate(self::LEADERBOARD_TODAY_WEEK_CACHE_KEY, $startDate, $endDate);
    }

    /**
     * 所有用戶的排行榜 - 月榜
     */
    public function makeTotalMonth()
    {
        $now = Carbon::now();
        $startDate = $now->startOfMonth()->format('Y-m-d');
        $endDate = $now->endOfMonth()->format('Y-m-d');

        $this->makeTotalDataByDate(self::LEADERBOARD_TODAY_MONTH_CACHE_KEY, $startDate, $endDate);
    }

    /**
     * 所有用戶的排行榜 - 總榜
     */
    public function makeTotalAll()
    {
        // 用戶總收禮金幣資料
        $limit = self::LEADERBOARD_USER_NUMBER;
        $receiveModelArray = $this->userRepository->scopeQuery(function ($query) use ($limit) {
            return $query->where([['accumulation_gift_gold_receive', '>', 0]])->orderBy('accumulation_gift_gold_receive', 'desc')->limit($limit);
        })->get()->all();

        $userIdToReceiveGold = [];
        $receiveResult = [];
        for ($i = 0; $i < count($receiveModelArray); $i++) {
            $receiveModel = $receiveModelArray[$i];

            $receiveResult[] = [
                'user_id' => $receiveModel->id,

                'pretty_id' => $receiveModel->pretty_id,
                'avatar' => $receiveModel->avatar,
                'level' => $receiveModel->current_level,
                'nick_name' => $receiveModel->nickname,
                'sex' => $receiveModel->sex,

                'price' => $receiveModel->accumulation_gift_gold_receive,
            ];
        }
        $receiveRedisKey = $this->getAnchorAllKey();
        \Cache::forever($receiveRedisKey, $receiveResult);

        // 用戶總送禮金幣資料
        $limit = self::LEADERBOARD_USER_NUMBER;
        $giveModelArray = $this->userRepository->scopeQuery(function ($query) use ($limit) {
            return $query->orderBy('accumulation_gift_gold_given', 'desc')->limit($limit);
        })->get()->all();

        $userIdToGiveGold = [];
        $giveResult = [];
        for ($i = 0; $i < count($giveModelArray); $i++) {
            $giveModel = $giveModelArray[$i];

            $giveResult[] = [
                'user_id' => $giveModel->id,

                'pretty_id' => $giveModel->pretty_id,
                'avatar' => $giveModel->avatar,
                'level' => $giveModel->current_level,
                'nick_name' => $giveModel->nickname,
                'sex' => $giveModel->sex,

                'price' => $giveModel->accumulation_gift_gold_given,
            ];
        }
        $giveRedisKey = $this->getFansAllKey();

        \Cache::forever($giveRedisKey, $giveResult);

    }

    /**
     * 增加直播間內的用戶消費禮物金幣數量
     */
    public function addUserGiftGoldToRoom($anchorId, $roomId, $userId, $giftGold)
    {
        $oldTopFiveLeaderboardData = $this->getLiveRoomTopFiveLeaderboardData($anchorId, $roomId);

        $key = $this->getUserInRoomKey($anchorId, $roomId, $userId);
        $userToGiftPurchaseData = \Cache::get($key);

        if ($userToGiftPurchaseData == null) {
            $userModel = $this->userRepository->findWhere(['id' => $userId])->first();

            $data = [
                'user_id' => $userModel->id,

                'pretty_id' => $userModel->id,
                'avatar' => $userModel->avatar,
                'level' => $userModel->current_level,
                'nick_name' => $userModel->nickname,
                'sex' => $userModel->sex,

                'price' => $giftGold,
            ];
            \Cache::forever($key, $data);
        } else {
            $userToGiftPurchaseData['price'] += $giftGold;
            \Cache::forever($key, $userToGiftPurchaseData);
        }
        // 將此用戶id加到直播間的快取user id list裡
        $userIdListKey = $this->getAllUserIdInRoomKey($anchorId, $roomId);

        $userIdListData = \Cache::get($userIdListKey);

        if ($userIdListData == null) {
            \Cache::forever($userIdListKey, [$userId]);
        } else if (!in_array($userId, $userIdListData)) {
            $userIdListData[] = $userId;
            \Cache::forever($userIdListKey, $userIdListData);
        }

        // 排行榜通知處理

        $newTopFiveLeaderboardData = $this->getLiveRoomTopFiveLeaderboardData($anchorId, $roomId);

        // 檢查排行榜是否有異動
        $needUpdate = false;

        if (count($newTopFiveLeaderboardData) != count($oldTopFiveLeaderboardData)) {
            $needUpdate = true;
        } else {
            $length = count($newTopFiveLeaderboardData);
            for ($i = 0; $i < $length; $i++) {
                if ($newTopFiveLeaderboardData[$i]['user_id'] != $oldTopFiveLeaderboardData[$i]['user_id']) {
                    $needUpdate = true;
                    break;
                }
            }
        }
        // 推送新的排行榜資料到直播間裡面
        if ($needUpdate) {
            $this->brocastLeaderboardDataToLiveRoomGroup($anchorId, app('im')::MESSAGE_TYPE_102, '');
        }

    }

    /**
     * 取得所有禮物資料, 並整理成 gift slug => model 的 array
     */
    private function getGiftSlugToModel()
    {
        $allGiftModelArray = $this->baseGiftTypeRepository->all()->all();
        $giftSlugToModel = [];
        for ($i = 0; $i < count($allGiftModelArray); $i++) {
            $giftSlugToModel[$allGiftModelArray[$i]->type_slug] = $allGiftModelArray[$i];
        }
        return $giftSlugToModel;
    }

    /**
     * 取得datetime時間資訊
     */
    private function getDatetimeInfo($startDate, $endDate)
    {
        if ($startDate == '' || $endDate == '') {
            $startDate = Carbon::now()->addDay(-1)->format('Y-m-d');
            $endDate = $startDate;
        }
        $startDatetime = $startDate . ' 00:00:00';
        $endDatetime = $endDate . ' 23:59:59';
        return [
            'startDatetime' => $startDatetime,
            'endDatetime' => $endDatetime,
        ];
    }

    /**
     * 依照主播id製作 個人排行榜 - 日榜
     */
    public function makePersonalTodayByAnchorId($anchorId)
    {
        $startDate = Carbon::now()->format('Y-m-d');
        $endDate = $startDate;
        $this->makePersonalByDate(self::LEADERBOARD_TODAY_DAY_CACHE_KEY, $startDate, $endDate, $anchorId);
    }

    /**
     * 依照主播id製作 個人排行榜 - 月榜
     */
    public function makePersonalMonthByAnchorId($anchorId)
    {
        $now = Carbon::now();
        $startDate = $now->startOfMonth()->format('Y-m-d');
        $endDate = $now->endOfMonth()->format('Y-m-d');

        $this->makePersonalByDate(self::LEADERBOARD_TODAY_MONTH_CACHE_KEY, $startDate, $endDate, $anchorId);
    }

    /**
     * 依照主播id製作 個人排行榜 - 總榜
     */
    public function makePersonalAllByAnchorId($anchorId)
    {
        $modelArray = $this->analyticAnchorReceiveGoldStatisticRepository->findWhere(['anchor_id' => $anchorId])->all();
        $this->makePersonalAllByModelArray($modelArray);
    }

    /**
     * 用指定的 model 來製作 個人排行榜 - 總榜
     */
    private function makePersonalAllByModelArray($modelArray)
    {
        // 所有用戶的id
        $allIdList = [];
        for ($i = 0; $i < count($modelArray); $i++) {
            $model = $modelArray[$i];
            $allIdList[$model->anchor_id] = $model->anchor_id;
            $allIdList[$model->give_gift_user_id] = $model->give_gift_user_id;
        }

        // id => user model
        $allUserModelArray = $this->userRepository->findWhereIn('id', $allIdList)->all();

        $idToUserModel = [];
        for ($i = 0; $i < count($allUserModelArray); $i++) {
            $userModel = $allUserModelArray[$i];
            $idToUserModel[$userModel->id] = $userModel;
        }

        $anchorIdToUserGiftData = [];
        for ($i = 0; $i < count($modelArray); $i++) {
            $model = $modelArray[$i];
            if (isset($anchorIdToUserGiftData[$model->anchor_id][$model->give_gift_user_id]) == false) {
                $anchorIdToUserGiftData[$model->anchor_id][$model->give_gift_user_id] = [
                    'user_id' => $model->give_gift_user_id,

                    'pretty_id' => $idToUserModel[$model->give_gift_user_id]->pretty_id,
                    'avatar' => $idToUserModel[$model->give_gift_user_id]->avatar,
                    'level' => $idToUserModel[$model->give_gift_user_id]->current_level,
                    'nick_name' => $idToUserModel[$model->give_gift_user_id]->nickname,
                    'sex' => $idToUserModel[$model->give_gift_user_id]->sex,

                    'price' => $model->total_give_gift_gold,
                ];
            } else {
                $anchorIdToUserGiftData[$model->anchor_id][$model->give_gift_user_id]['price'] += $model->total_give_gift_gold;
            }
        }

        $result = [];
        foreach ($anchorIdToUserGiftData as $anchorId => $data) {
            // 排序 - price 越大越前面
            usort($anchorIdToUserGiftData[$anchorId], function ($a, $b) {
                return $b['price'] - $a['price'];
            });
            // 取出前 50 筆資料
            $result[$anchorId] = array_slice($anchorIdToUserGiftData[$anchorId], 0, self::LEADERBOARD_USER_NUMBER);
        }

        // 個人排行榜寫入redis
        foreach ($result as $anchorId => $data) {
            $key = $this->getPersonalAllKey($anchorId);
            \Cache::forever($key, $data);
        }
    }

    /**
     * 依照日期製作個人排行榜資料
     */
    private function makePersonalByDate($redisKey = '', $startDate = '', $endDate = '', $anchorId = '')
    {
        //取得時間範圍
        $datetimeInfo = $this->getDatetimeInfo($startDate, $endDate);
        $startDatetime = $datetimeInfo['startDatetime'];
        $endDatetime = $datetimeInfo['endDatetime'];

        // 取得所有禮物資料
        $giftSlugToModel = $this->getGiftSlugToModel();

        if ($anchorId != '') {
            $where = [
                ['receive_uid', '=', $anchorId],
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ];
        } else {
            $where = [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ];
        }

        // 取得所有交易資料
        $allCollection = $this->giftTransactionOrderRepository->findWhere($where);
        $allTransactionOrderModelArray = $allCollection->all();

        // 所有用戶的id
        $allIdList = [];
        for ($i = 0; $i < count($allTransactionOrderModelArray); $i++) {
            $orderModel = $allTransactionOrderModelArray[$i];
            $allIdList[$orderModel->receive_uid] = $orderModel->receive_uid;
            $allIdList[$orderModel->give_uid] = $orderModel->give_uid;
        }

        // id => user model
        $allUserModelArray = $this->userRepository->findWhereIn('id', $allIdList)->all();
        $idToUserModel = [];
        for ($i = 0; $i < count($allUserModelArray); $i++) {
            $userModel = $allUserModelArray[$i];
            $idToUserModel[$userModel->id] = $userModel;
        }

        $anchorToFansArray = [];
        for ($i = 0; $i < count($allTransactionOrderModelArray); $i++) {
            $orderModel = $allTransactionOrderModelArray[$i];
            $price = $giftSlugToModel[$orderModel->gift_type_id]->gold_price;

            if (isset($anchorToFansArray[$orderModel->receive_uid][$orderModel->give_uid])) {
                $anchorToFansArray[$orderModel->receive_uid][$orderModel->give_uid]['price'] += $price;
            } else {
                $anchorToFansArray[$orderModel->receive_uid][$orderModel->give_uid] = [
                    'user_id' => $orderModel->give_uid,
                    'pretty_id' => $idToUserModel[$orderModel->give_uid]->pretty_id,
                    'avatar' => $idToUserModel[$orderModel->give_uid]->avatar,
                    'level' => $idToUserModel[$orderModel->give_uid]->current_level,
                    'nick_name' => $idToUserModel[$orderModel->give_uid]->nickname,
                    'sex' => $idToUserModel[$orderModel->give_uid]->sex,
                    'price' => $price,

                ];
            }
        }

        $result = [];
        foreach ($anchorToFansArray as $anchorId => $fansData) {
            // 排序 - price 越大越前面統計
            usort($anchorToFansArray[$anchorId], function ($a, $b) {
                return $b['price'] - $a['price'];
            });
            // 取出前 50 筆資料
            $result[$anchorId] = array_slice($anchorToFansArray[$anchorId], 0, self::LEADERBOARD_USER_NUMBER);
        }

        // 個人排行榜 redis key 的 prefix
        if ($redisKey == '') {
            $prefixRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY;
        } else {
            $prefixRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_PERSONAL_CACHE_KEY . ':' . $redisKey;
        }

        // 個人排行榜寫入redis
        foreach ($result as $anchorId => $data) {
            $key = $prefixRedisKey . ':' . $anchorId;
            \Cache::forever($key, $data);
        }
    }

    /**
     * 依照日期製作排行榜
     */
    private function makeTotalDataByDate($redisKey = '', $startDate = '', $endDate = '')
    {
        $datetimeInfo = $this->getDatetimeInfo($startDate, $endDate);
        $startDatetime = $datetimeInfo['startDatetime'];
        $endDatetime = $datetimeInfo['endDatetime'];

        // 取得所有禮物資料
        $giftSlugToModel = $this->getGiftSlugToModel();

        // 取得所有交易資料
        $allCollection = $this->giftTransactionOrderRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );
        $allTransactionOrderModelArray = $allCollection->all();

        // 所有用戶的id
        $allIdList = [];
        for ($i = 0; $i < count($allTransactionOrderModelArray); $i++) {
            $orderModel = $allTransactionOrderModelArray[$i];
            $allIdList[$orderModel->receive_uid] = $orderModel->receive_uid;
            $allIdList[$orderModel->give_uid] = $orderModel->give_uid;
        }

        // id => user model
        $allUserModelArray = $this->userRepository->findWhereIn('id', $allIdList)->all();
        $idToUserModel = [];
        for ($i = 0; $i < count($allUserModelArray); $i++) {
            $userModel = $allUserModelArray[$i];
            $idToUserModel[$userModel->id] = $userModel;
        }

        // 計算 收/送 金幣數量
        $userIdToReceiveGold = [];
        $userIdToGivenGold = [];
        for ($i = 0; $i < count($allTransactionOrderModelArray); $i++) {
            $orderModel = $allTransactionOrderModelArray[$i];
            $price = $giftSlugToModel[$orderModel->gift_type_id]->gold_price;

            // 加總收禮金幣數
            if (isset($userIdToReceiveGold[$orderModel->receive_uid])) {
                $userIdToReceiveGold[$orderModel->receive_uid]['price'] += $price;
            } else {
                $userIdToReceiveGold[$orderModel->receive_uid] = [
                    'user_id' => $orderModel->receive_uid,
                    'pretty_id' => $idToUserModel[$orderModel->receive_uid]->pretty_id,

                    'avatar' => $idToUserModel[$orderModel->receive_uid]->avatar,
                    'level' => $idToUserModel[$orderModel->receive_uid]->current_level,
                    'nick_name' => $idToUserModel[$orderModel->receive_uid]->nickname,
                    'sex' => $idToUserModel[$orderModel->receive_uid]->sex,
                    'price' => floatval($price),
                ];
            }

            // 加總送禮金幣數
            if (isset($userIdToGivenGold[$orderModel->give_uid])) {
                $userIdToGivenGold[$orderModel->give_uid]['price'] += $price;
            } else {
                $userIdToGivenGold[$orderModel->give_uid] = [
                    'user_id' => $orderModel->give_uid,
                    'pretty_id' => $idToUserModel[$orderModel->give_uid]->pretty_id,

                    'avatar' => $idToUserModel[$orderModel->give_uid]->avatar,
                    'level' => $idToUserModel[$orderModel->give_uid]->current_level,
                    'nick_name' => $idToUserModel[$orderModel->give_uid]->nickname,
                    'sex' => $idToUserModel[$orderModel->give_uid]->sex,
                    'price' => floatval($price),
                ];
            }
        }

        // 排序 越大越前面
        usort($userIdToReceiveGold, function ($a, $b) {
            return $b['price'] - $a['price'];
        });
        usort($userIdToGivenGold, function ($a, $b) {
            return $b['price'] - $a['price'];
        });

        // 收禮排行榜 redis key
        if ($redisKey == '') {
            $anchorRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY;
        } else {
            $anchorRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_ANCHOR_CACHE_KEY . ':' . $redisKey;
        }
        // 收禮排行榜寫入redis
        $receiveResult = array_slice($userIdToReceiveGold, 0, self::LEADERBOARD_USER_NUMBER);
        \Cache::forever($anchorRedisKey, $receiveResult);

        // 送禮排行榜 redis key
        if ($redisKey == '') {
            $fansRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY;
        } else {
            $fansRedisKey = self::LEADERBOARD_CACHE_KEY . ':' . self::LEADERBOARD_FANS_CACHE_KEY . ':' . $redisKey;
        }
        // 送禮排行榜寫入redis
        $giveResult = array_slice($userIdToGivenGold, 0, self::LEADERBOARD_USER_NUMBER);
        \Cache::forever($fansRedisKey, $giveResult);
    }

    /**
     * 廣播排行榜資訊到直播間聊天群組裡
     */
    // TODO 考虑 通过 call API
    private function brocastLeaderboardDataToLiveRoomGroup($groupId, $code, $leaderboardData)
    {
        $jsonData = '';
        if ($leaderboardData != '') {
            $jsonData = json_encode($leaderboardData);
        }
        $broadCastData = [
            'GroupId' => (string) $groupId,
            'MsgBody' => [
                [
                    'MsgType' => 'TIMCustomElem',
                ],
            ],
        ];
        $result = \IM::sendLiveRoomBroadcast($broadCastData, [['msg' => batchReplaceLocaleByArray('im_message.' . $code, ['leaderboardData' => $jsonData])]]);
    }

    /**
     * 取得前 n 名的排行榜資料
     */
    private function getgetLiveRoomLeaderboardDataWithNumber($anchorId, $roomId, $number)
    {
        if ($number < self::LEADERBOARD_MIN_USER_NUMBER) {
            $number = self::LEADERBOARD_MIN_USER_NUMBER;
        }
        if ($number > self::LEADERBOARD_MAX_USER_NUMBER) {
            $number = self::LEADERBOARD_MAX_USER_NUMBER;
        }

        $prefix = $this->getLiveRoomPrefixKey($anchorId, $roomId);
        $allIdsKey = $this->getAllUserIdInRoomKey($anchorId, $roomId);

        // 取得所有用戶的id
        $allIds = \Cache::get($allIdsKey);
        if ($allIds == null || $allIds == []) {
            return [];
        }

        // 依照id組成redis key
        $allKeys = [];
        for ($i = 0; $i < count($allIds); $i++) {
            $allKeys[] = $prefix . ":" . $allIds[$i];
        }
        $allData = \Cache::many($allKeys);

        // 排序, 大到小
        usort($allData, function ($a, $b) {
            return $b['price'] - $a['price'];
        });

        // 取出前 n 名
        $result = array_slice($allData, 0, $number);
        return $result;
    }

}
