<?php

namespace App\Services;

use App\Exceptions\ErrorCode;
use App\Models\LiveRoom as LiveRoomModel;
use App\Models\User as UserModel;
use App\Repositories\Interfaces\AnalyticAnchorReceiveGoldStatisticRepository;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;
use App\Repositories\Interfaces\LiveGiftStatisticsRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\ManagerCompanyGoldFlowRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\UserGoldFlowRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Services\LiveRoom;
use App\Services\UserLevelService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

//礼物服务
class GiftService
{
    private $userRepository;
    private $baseGiftTypeRepository;
    private $giftTransactionOrderRepository;
    private $userGoldFlowRepository;
    private $managerRepository;
    private $anchorInfoRepository;
    private $managerCompanyGoldFlowRepository;
    private $analyticAnchorReceiveGoldStatisticRepository;
    private $liveGiftStatisticsRepository;

    public function __construct(
        UserRepository $userRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        GiftTransactionOrderRepository $giftTransactionOrderRepository,
        UserGoldFlowRepository $userGoldFlowRepository,
        LiveRoomRepository $liveRoomRepository,
        ManagerRepository $managerRepository,
        AnchorInfoRepository $anchorInfoRepository,
        ManagerCompanyGoldFlowRepository $managerCompanyGoldFlowRepository,
        AnalyticAnchorReceiveGoldStatisticRepository $analyticAnchorReceiveGoldStatisticRepository,
        LiveGiftStatisticsRepository $liveGiftStatisticsRepository) {
        $this->userRepository = $userRepository;
        $this->baseGiftTypeRepository = $baseGiftTypeRepository;
        $this->giftTransactionOrderRepository = $giftTransactionOrderRepository;
        $this->userGoldFlowRepository = $userGoldFlowRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->managerRepository = $managerRepository;
        $this->anchorInfoRepository = $anchorInfoRepository;
        $this->managerCompanyGoldFlowRepository = $managerCompanyGoldFlowRepository;
        $this->analyticAnchorReceiveGoldStatisticRepository = $analyticAnchorReceiveGoldStatisticRepository;
        $this->liveGiftStatisticsRepository = $liveGiftStatisticsRepository;
    }

    public function getDefaultGiftData()
    {
        return $this->baseGiftTypeRepository->model()::getDefaultData();
    }

    /**
     * 依照 id 來取得禮物資料
     */
    public function findById($giftId)
    {
        return $this->baseGiftTypeRepository->findWhere(['id' => $giftId]);
    }

    /**
     * 取得所有禮物列表
     *
     * @return Collection
     */
    function list(): Collection{
        $collections = $this->baseGiftTypeRepository->all();
        //dd($collections);
        return $collections;
    }

    /**
     * 取得所有禮物的asset資源
     */
    public function assetsList()
    {
        $collections = $this->baseGiftTypeRepository->all();
        $allModels = $collections->all();

        $result = [];

        for ($i = 0; $i < count($allModels); $i++) {
            $giftId = $allModels[$i]->id;
            $giftIdPaded = sprintf("%06d", $giftId);

            $version = Carbon::parse($allModels[$i]->updated_at)->timestamp;
            //$version = $allModels[$i]->updated_at;
            $fileName = $giftIdPaded;

            $result[] = [
                'gift_id' => $giftId,
                'local_file_name' => $fileName,
                'version' => strval($version),
                'image_link' => $allModels[$i]->image,
                'svga_link' => $allModels[$i]->svg,
            ];
        }
        return $result;
    }

    /**
     * 新增一筆禮物資料
     *
     * @param array $parameters
     */
    public function create($parameters)
    {
        // 取得熱度資訊
        $hotInfo = $this->getHotInfoByParameters($parameters);
        $now = date("Y-m-d H:i:s", time());

        // 檢查 propotion 資料是否合格
        if (!$this->checkPropotionDataIsValid($parameters['propotion_list'])) {
            throw new \Exception('invalid propotion data');
        }

        // 準備寫入資料表 base_gift_type 的資料
        $data = [
            'name' => $parameters['name'],
            'type_slug' => $parameters['type_slug'],
            'gold_price' => $parameters['price'],
            'comment' => $parameters['comment'],
            'image' => $parameters['image'],
            'svg' => $parameters['svg'],
            'onshow' => $parameters['onshow'],
            'is_prop' => $parameters['is_prop'],
            'is_mission' => $parameters['is_mission'],
            'is_big' => $parameters['is_big'],
            'hot_value' => $hotInfo['hot_value'],
            'hot_time' => $hotInfo['hot_time'],
            'propotion_list' => $parameters['propotion_list'],
            'created_at' => $now,
            'updated_at' => $now,
        ];

        // 寫入資料表 base_gift_type
        $newGiftId = $this->baseGiftTypeRepository->create($data)->id;
    }

    /**
     * 更新一筆禮物資料
     */
    public function update($parameters)
    {
        // 取得熱度資訊
        $hotInfo = $this->getHotInfoByParameters($parameters);

        // 檢查 propotion 資料是否合格
        if (!$this->checkPropotionDataIsValid($parameters['propotion_list'])) {
            throw new \Exception('invalid propotion data');
        }

        $record = $this->findById($parameters['id']);
        if ($record->count() == 0) {
            throw new \Exception('invalid gift id');
        }

        $record = $record->first();
        $record->name = isset($parameters['name']) ? $parameters['name'] : $record->name;
        $record->weight = isset($parameters['weight']) ? $parameters['weight'] : $record->weight;
        $record->gold_price = isset($parameters['price']) ? $parameters['price'] : $record->gold_price;
        $record->comment = isset($parameters['comment']) ? $parameters['comment'] : $record->comment;

        if (isset($parameters['image'])) {
            $record->image = $parameters['image'];
        }
        if (isset($parameters['svg'])) {
            $record->svg = $parameters['svg'];
        }

        $record->onshow = isset($parameters['onshow']) ? $parameters['onshow'] : $record->onshow;
        $record->is_prop = isset($parameters['is_prop']) ? $parameters['is_prop'] : $record->is_prop;
        $record->is_mission = isset($parameters['is_mission']) ? $parameters['is_mission'] : $record->is_mission;
        $record->is_big = isset($parameters['is_big']) ? $parameters['is_big'] : $record->is_big;
        $record->hot_value = isset($parameters['hot_value']) ? $parameters['hot_value'] : $record->hot_value;
        $record->hot_time = isset($parameters['hot_time']) ? $parameters['hot_time'] : $record->hot_time;
        $record->propotion_list = isset($parameters['propotion_list']) ? $parameters['propotion_list'] : $record->propotion_list;
        $record->save();
    }

    /**
     * 將 collection 資料依照禮物類型來分類
     * @param array $collectionArray
     *
     * @return array
     */
    public function arrangeListDataForGiftType($collectionArray)
    {
        $result = array();
        $gift_list = array();
        $prop_list = array();
        $big_gift_list = array();

        // 將DB的資料,整理為要輸出的格式
        for ($i = 0; $i < count($collectionArray); $i++) {
            $collection = $collectionArray[$i];

            // 若禮物設定為不顯示, 不輸出
            if ($collection['onshow'] == false) {
                continue;
            }
            $data = array();
            // 回傳的id使用slug
            $data['gift_id'] = $collection['type_slug'];

            $data['gift_name'] = $collection['name'];
            $data['comment'] = $collection['comment'];
            $data['price'] = $collection['gold_price'];
            $data['image'] = $collection['image'];
            $data['svg'] = $collection['svg'];
            $data['is_big'] = $collection['is_big'];

            // 依照道具(prop), 大禮物(big), 一般禮物來區分
            if ($collection['is_prop']) {
                array_push($prop_list, $data);
            } else if ($collection['is_big']) {
                array_push($big_gift_list, $data);
            } else {
                array_push($gift_list, $data);
            }
        }
        $result['gift'] = $gift_list;
        $result['prop'] = $prop_list;
        $result['big_gift'] = $big_gift_list;

        return $result;
    }

    /**
     * 購買禮物
     *
     * @param int $userId
     * @param int $roomId
     * @param int $giftSlug
     *
     * @return array
     */
    public function purchase($userId, $roomId, $giftSlug): array
    {
        // 檢查並取得購買相關的model
        $modelResult = $this->getModelAndCheckPurchase($userId, $roomId, $giftSlug);

        $userModel = $modelResult['user'];
        $roomModel = $modelResult['room'];
        $giftModel = $modelResult['gift'];

        $remainGold = $userModel->gold;

        // 用戶金幣不夠, 回傳 USER_GOLD_NOT_ENOUGH 狀態
        if ($userModel->gold_cache < $giftModel->gold_price) {
            throw new \Exception(__('user.gold_not_enough'), ErrorCode::USER_GOLD_NOT_ENOUGH);
        }

        if ($giftModel->is_mission == false) {
            // 一般禮物購買處理
            $remainGold = $this->purchaseNormalGift($userModel, $roomModel, $giftModel);
        } else {
            // 任務禮物購買處理
            $remainGold = $this->purchaseMissionGift($userModel, $roomModel, $giftModel);
        }

        return ['status' => ErrorCode::OK, 'remain_gold' => $remainGold];
    }

    /**
     * 取得購買會使用到的Model, 同時做一些基本的驗證與檢查
     *
     * @param int $userId
     * @param int $roomId
     * @param int $giftSlug
     */
    protected function getModelAndCheckPurchase($userId, $roomId, $giftSlug)
    {
        // 送禮的用戶 id 不存在, 回傳error
        $userCollection = $this->userRepository->findWhere(['id' => $userId]);
        $userModel = $userCollection->first();
        if ($userModel == null) {
            throw new \Exception(__('gift.notFoundUser'));
        }

        $roomCollection = $this->liveRoomRepository->findWhere(['id' => $roomId]);
        $roomModel = $roomCollection->first();
        // 直播間不存在, 回傳error
        if ($roomModel == null) {
            throw new \Exception(__('gift.notFoundLiveRoom'));
        }
        // 直播間狀態不是開播中, 回傳error
        if ($roomModel->status != LiveRoomModel::STATUS_LIVE) {
            throw new \Exception(__('gift.roomNotLive'));
        }

        $giftCollection = $this->baseGiftTypeRepository->findWhere(['type_slug' => $giftSlug]);
        $giftModel = $giftCollection->first();
        // 禮物id不存在, 回傳error
        if ($giftModel == null) {
            throw new \Exception(__('gift.giftIdNotFound'));
        }
        return [
            'user' => $userModel,
            'room' => $roomModel,
            'gift' => $giftModel,
        ];
    }

    /**
     * 取得基本要寫入禮物交易紀錄DB的資料
     * @param Model $userModel
     * @param Model $roomModel
     * @param Model $giftModel
     */
    protected function getBasicGiftTransactionOrderData($userModel, $roomModel, $giftModel)
    {
        // 取得現在時間
        $now = Carbon::now();

        // 準備要寫入送禮交易資料表的資料
        $order = [
            'room_id' => $roomModel->id,
            'give_uid' => $userModel->id,
            'receive_uid' => $roomModel->user_id,
            'gift_type_id' => $giftModel->type_slug,
            'gold_remain' => $userModel->gold - $giftModel->gold_price,
            'gold_price' => $giftModel->gold_price,
            'hot_value' => 0,
            'hot_expired_time' => $now,
        ];
        // 紀錄禮物的熱度
        if ($giftModel->is_prop && $giftModel->hot_value > 0 && $giftModel->hot_time != 0) {
            $order['hot_value'] = $giftModel->hot_value;
            if ($giftModel->hot_time > 0) {
                $order['hot_expired_time'] = $now->addSeconds($giftModel->hot_time);
            } else if ($giftModel->hot_time < 0) {
                $order['hot_expired_time'] = $now->addDays(365);
            }
        }

        return $order;
    }

    /**
     * 購買一般禮物
     * @param Model $userModel
     * @param Model $roomModel
     * @param Model $giftModel
     *
     * @return double
     */
    protected function purchaseNormalGift($userModel, $roomModel, $giftModel)
    {
        $gold = -1 * $giftModel->gold_price;
        $userGold = $userModel->gold_cache - $giftModel->gold_price;
        $this->userRepository->addGold($userModel, $gold, null, true, function ($cacheKey, $fieldKey) use ($userModel, $roomModel, $giftModel) {
            \Queue::pushOn(pool('gift'), new \App\Jobs\SendGift($userModel, $roomModel, $giftModel, $cacheKey, $fieldKey));
        });
        return $userGold;
    }

    /**
     * 原来的送礼的第二阶段
     *
     * @param    App\Models\User                   $userModel [description]
     * @param    App\Models\LiveRooms                   $roomModel [description]
     * @param    App\Models\Gift                   $giftModel [description]
     *
     * @return   void                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-02T17:19:18+0800
     */
    public function purchaseNormalGiftJob($userModel, $roomModel, $giftModel, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userModel->id])->first();

        // 準備要寫入送禮交易資料表的資料
        $order = $this->getBasicGiftTransactionOrderData($userModel, $roomModel, $giftModel);

        // 依照佔成計算真實可獲得的金幣數量
        $realObtainGold = $this->getRealObtainGoldByPropotion($giftModel->gold_price, $giftModel->propotion_list[0]);

        // 實際取得金額和當時的佔成設定
        $order['anchor_real_receive_gold'] = $realObtainGold['anchor'];
        $order['company_real_receive_gold'] = $realObtainGold['company'];
        $order['propotion_setting'] = json_encode($giftModel->propotion_list[0]);

        // 將交易紀錄寫入資料表
        $giftTransactionOrderModel = $this->giftTransactionOrderRepository->create($order);

        // 更新用戶金幣數量
        $userGold = $userModel->gold - $giftModel->gold_price;
        $this->userRepository->addGold($userModel, -1 * $giftModel->gold_price, $giftTransactionOrderModel);

        // 累加用戶的送禮金幣數量
        $userModel->accumulation_gift_gold_given = $userModel->accumulation_gift_gold_given + $giftModel->gold_price;
        $userModel->save();

        // // 依照佔成計算真實可獲得的金幣數量
        $realObtainGold = $this->getRealObtainGoldByPropotion($giftModel->gold_price, $giftModel->propotion_list[0]);

        // 取得主播的用戶資料
        $anchorId = $roomModel->user_id;
        $anchorCollection = $this->userRepository->FindByField('id', $anchorId);

        $anchorModel = $anchorCollection->first();

        // 取得主播資訊
        $anchorInfoCollection = $this->anchorInfoRepository->FindByField('user_id', $anchorId);
        $anchorInfoModel = $anchorInfoCollection->first();

        // 取得主播經濟公司的資料
        $managerCompanyCollection = $this->managerRepository->FindByField('id', $anchorInfoModel->company_id);
        $managerCompanyModel = $managerCompanyCollection->first();

        // 更新主播的金幣數量
        $anchorGold = $anchorModel->gold + $realObtainGold['anchor'];
        $this->userRepository->addGold($anchorModel, $realObtainGold['anchor'], $giftTransactionOrderModel);

        // 累加主播的收禮金幣數量
        $anchorModel->accumulation_gift_gold_receive = $anchorModel->accumulation_gift_gold_receive + $giftModel->gold_price;
        $anchorModel->save();

        // 更新經濟公司的金幣數量
        $managerGold = $managerCompanyModel->gold + $realObtainGold['company'];
        $this->managerRepository->updateGold($managerCompanyModel, $managerGold, $giftTransactionOrderModel);

        // 累加用戶對這位主播的送禮金幣數量
        $this->addAnalyticAnchorReceiveGoldStatistic($anchorId, $userModel->id, $giftModel->gold_price);

        // 累加本次直播的總收金幣數
        $roomTotalReceiveGold = $roomModel->total_receive_gold + $giftModel->gold_price;
        $roomModel->total_receive_gold = $roomTotalReceiveGold;
        $roomModel->total_real_receive_gold += $realObtainGold['anchor'];
        $roomModel->save();

        // 更新直播間內的用戶消費資料
        $leaderboardService = app(LeaderboardService::class);
        $leaderboardService->addUserGiftGoldToRoom($anchorId, $roomModel->id, $userModel->id, $giftModel->gold_price);

        // 取得該直播間目前的熱度

        $hotInfoKey = LiveRoom::getHotInfoCacheKey($roomModel->id);
        $currentHotValue = \Cache::get($hotInfoKey);
        if ($currentHotValue == null) {
            $currentHotValue = 0;
        }
        $currentHotValue += $giftModel->hot_value;
        $liveRoomService = app(LiveRoom::class);
        $liveRoomService->pushGiftToHotList($roomModel->id, $giftTransactionOrderModel);
        //廣播總收禮金幣與熱度資料資料到直播間裡

        $liveRoomService->brocastGoldAnHotDataToLiveRoom($anchorId, $roomTotalReceiveGold, $currentHotValue);

        // 增加主播與送禮粉絲的經驗
        $levelService = app(UserLevelService::class);
        $levelService->addExpByGivenGift($userModel->id, $giftModel->gold_price);
        $levelService->addExpByReceiveGift($anchorId, $giftModel->gold_price);

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);

        // 累計禮物次數
        $liveGiftStatisticsModel = $this->liveGiftStatisticsRepository->findWhere(
            [
                'room_id' => $roomModel->id,
                'gift_type_slug' => $giftModel->type_slug,
            ]
        )->first();
        if ($liveGiftStatisticsModel == null) {
            $data = [
                'room_id' => $roomModel->id,
                'anchor_id' => $anchorId,
                'gift_type_slug' => $giftModel->type_slug,
                'gift_name' => $giftModel->name,
                'gift_price' => $giftModel->gold_price,
                'count' => 1,
            ];
            $this->liveGiftStatisticsRepository->create($data);
        } else {
            $liveGiftStatisticsModel->count += 1;
            $liveGiftStatisticsModel->save();
        }
    }
    /**
     * 增加每一位用戶送主播禮物的統計數據
     */
    public function addAnalyticAnchorReceiveGoldStatistic($anchorId, $givUserId, $gold)
    {
        $statisticCollection = $this->analyticAnchorReceiveGoldStatisticRepository->findWhere(
            [
                'anchor_id' => $anchorId,
                'give_gift_user_id' => $givUserId,
            ]
        );
        if ($statisticCollection->count() == 0) {
            $data = [
                'anchor_id' => $anchorId,
                'give_gift_user_id' => $givUserId,
                'total_give_gift_gold' => $gold,
            ];
            $this->analyticAnchorReceiveGoldStatisticRepository->create($data);
        } else {
            $statisticModel = $statisticCollection->first();
            $statisticModel->total_give_gift_gold += $gold;
            $statisticModel->save();
        }
    }

    /**
     * 購買任務型禮物
     * @param Model $userModel
     * @param Model $roomModel
     * @param Model $giftModel
     *
     * @return double
     */
    protected function purchaseMissionGift($userModel, $roomModel, $giftModel)
    {
        $gold = -1 * $giftModel->gold_price;
        $userGold = $userModel->gold_cache - $giftModel->gold_price;
        $this->userRepository->addGold($userModel, $gold, null, true, function ($cacheKey, $fieldKey) use ($userModel, $roomModel, $giftModel) {
            \Queue::pushOn(pool('gift'), new \App\Jobs\SendMissionGift($userModel, $roomModel, $giftModel, $cacheKey, $fieldKey));
        });

        //回傳用戶餘額
        return $userGold;
    }

    /**
     * 购买任务礼物后续
     *
     * @param    [type]                   $userModel [description]
     * @param    [type]                   $roomModel [description]
     * @param    [type]                   $giftModel [description]
     *
     * @return   [type]                              [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-10-03T13:56:21+0800
     */
    public function purchaseMissionGiftJob($userModel, $roomModel, $giftModel, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userModel->id])->first();

        // 準備要寫入送禮交易資料表的資料
        $order = $this->getBasicGiftTransactionOrderData($userModel, $roomModel, $giftModel);
        // 將交易紀錄寫入資料表
        $giftTransactionOrderModel = $this->giftTransactionOrderRepository->create($order);
        // 更新用戶金幣數量
        $userGold = $userModel->gold - $giftModel->gold_price;
        $this->userRepository->addGold($userModel, -1 * $giftModel->gold_price, $giftTransactionOrderModel);

        // 累加用戶的送禮金幣數量
        $userModel->accumulation_gift_gold_given = $userModel->accumulation_gift_gold_given + $giftModel->gold_price;
        $userModel->save();

        // 取得主播的用戶資料
        $anchorId = $roomModel->user_id;
        $anchorCollection = $this->userRepository->FindByField('id', $anchorId);
        $anchorModel = $anchorCollection->first();

        // 累加主播的收禮金幣數量
        $anchorModel->accumulation_gift_gold_receive = $anchorModel->accumulation_gift_gold_receive + $giftModel->gold_price;
        $anchorModel->save();

        // 取得主播的用戶資料
        $anchorId = $roomModel->user_id;
        $anchorCollection = $this->userRepository->FindByField('id', $anchorId);
        $anchorModel = $anchorCollection->first();

        // 累加主播的收禮金幣數量
        $anchorModel->accumulation_gift_gold_receive = $anchorModel->accumulation_gift_gold_receive + $giftModel->gold_price;
        $anchorModel->save();

        // 累加用戶對這位主播的送禮金幣數量
        $this->addAnalyticAnchorReceiveGoldStatistic($anchorId, $userModel->id, $giftModel->gold_price);

        // 累加本次直播的總收金幣數
        $roomTotalReceiveGold = $roomModel->total_receive_gold + $giftModel->gold_price;
        $roomModel->total_receive_gold = $roomTotalReceiveGold;
        //$roomModel->total_real_receive_gold += $realObtainGold['anchor'];
        $roomModel->save();

        // 更新直播間內的用戶消費資料
        $leaderboardService = app(LeaderboardService::class);
        $leaderboardService->addUserGiftGoldToRoom($anchorId, $roomModel->id, $userModel->id, $giftModel->gold_price);

        // 取得該直播間目前的熱度
        $hotInfoKey = LiveRoom::getHotInfoCacheKey($roomModel->id);
        $currentHotValue = \Cache::get($hotInfoKey);
        if ($currentHotValue == null) {
            $currentHotValue = 0;
        }
        $currentHotValue += $giftModel->hot_value;
        $liveRoomService = app(LiveRoom::class);
        $liveRoomService->pushGiftToHotList($roomModel->id, $giftTransactionOrderModel);
        //廣播總收禮金幣資料到直播間裡
        $liveRoomService->brocastGoldAnHotDataToLiveRoom($anchorId, $roomTotalReceiveGold, $currentHotValue);

        // 增加主播與送禮粉絲的經驗
        $levelService = app(UserLevelService::class);
        $levelService->addExpByGivenGift($userModel->id, $giftModel->gold_price);
        $levelService->addExpByReceiveGift($anchorId, $giftModel->gold_price);

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);

        // 累計禮物次數
        $liveGiftStatisticsModel = $this->liveGiftStatisticsRepository->findWhere(
            [
                'room_id' => $roomModel->id,
                'gift_type_slug' => $giftModel->type_slug,
            ]
        )->first();
        if ($liveGiftStatisticsModel == null) {
            $data = [
                'room_id' => $roomModel->id,
                'anchor_id' => $anchorId,
                'gift_type_slug' => $giftModel->type_slug,
                'gift_name' => $giftModel->name,
                'gift_price' => $giftModel->gold_price,
                'count' => 1,
            ];
            $this->liveGiftStatisticsRepository->create($data);
        } else {
            $liveGiftStatisticsModel->count += 1;
            $liveGiftStatisticsModel->save();
        }
    }
    /**
     * 依照佔成來取得實際可獲得的金幣數量
     *
     * @param double $gold
     * @param array $propotion
     */
    protected function getRealObtainGoldByPropotion($gold, $propotion)
    {
        $rate = $this->getPropotionRate($propotion);

        // 計算主播實際可得金額, 小數點第2位, 4捨5入
        $anchorRealObtainGold = round($gold * $rate['anchor'], 2);

        // 計算經濟公司實際可得金額, 小數點第2位, 4捨5入
        $companyRealObtainGold = round($gold * $rate['company'], 2);

        return ['anchor' => $anchorRealObtainGold, 'company' => $companyRealObtainGold];
    }

    /**
     * 計算佔成比例
     *
     * @param array $propotion
     */
    protected function getPropotionRate($propotion)
    {
        // 計算主播分成比例
        $anchorRate = $propotion['anchor_propotion'] / 100.0;
        if ($anchorRate > 1) {
            $anchorRate = 1;
        }
        if ($anchorRate < 0) {
            $anchorRate = 0;
        }

        // 計算經濟公司分成比例
        $companyRate = $propotion['company_propotion'] / 100.0;
        // 如果經濟公司和主播比例相加大於1, 修正經濟公司的比例
        if ($anchorRate + $companyRate > 1) {
            $companyRate = 1 - $anchorRate;
        }
        if ($companyRate > 1) {
            $companyRate = 1;
        }
        if ($anchorRate < 0) {
            $companyRate = 0;
        }
        return ['anchor' => $anchorRate, 'company' => $companyRate];
    }

    /**
     * 從輸入的參數取得熱度資訊
     *
     * @param array $parameters
     *
     * @return array
     */
    protected function getHotInfoByParameters($parameters): array
    {
        //預設 熱度為0
        $hotValue = 0;
        $hotTime = 0;

        //如果為道具, 依據輸入參數讀取熱度設定
        if ($parameters['is_prop'] == 1) {
            if (isset($parameters['hot_value'])) {
                $hotValue = $parameters['hot_value'];
            }
            if (isset($parameters['hot_time'])) {
                $hotTime = $parameters['hot_time'];
            }
        }
        return ['hot_value' => $hotValue, 'hot_time' => $hotTime];
    }

    /**
     * 從輸入的參數取得佔成的json string
     */
    protected function getPropotionJsonStringByParameters($parameters): string
    {
        $prpotionDataList = [];
        for ($i = 0; $i < count($parameters['propotion_list']); $i++) {
            $propotion = [];
            $propotion['receive_times'] = $parameters['propotion_list'][$i]['receive_times'];
            $propotion['anchor_propotion'] = $parameters['propotion_list'][$i]['anchor_propotion'];
            $propotion['company_propotion'] = $parameters['propotion_list'][$i]['company_propotion'];
            array_push($prpotionDataList, $propotion);
        }
        $propotionJsonString = json_encode($prpotionDataList);
        return $propotionJsonString;
    }

    /**
     * 檢查佔成的資料格式是否正確
     */
    protected function checkPropotionDataIsValid($propotionData): bool
    {
        $cheker = [];
        // 檢查佔成資料裡面, 是否有相同的 receive_times, 若有回傳false(不合法)
        for ($i = 0; $i < count($propotionData); $i++) {
            $receiveTimes = $propotionData[$i]['receive_times'];
            if (isset($cheker[$receiveTimes])) {
                return false;
            } else {
                $cheker[$receiveTimes] = 1;
            }
        }
        return true;
    }
}
