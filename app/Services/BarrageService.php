<?php

namespace App\Services;

use App\Exceptions\ErrorCode;
use App\Models\BaseBarrageType as BaseBarrageTypeModel;
use App\Models\LiveRoom as LiveRoomModel;
use App\Repositories\Interfaces\BarrageTransactionOrderRepository;
use App\Repositories\Interfaces\BaseBarrageTypeRepository;
use App\Repositories\Interfaces\LiveBarrageStatisticsRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\UserConfigRepository;
use App\Repositories\Interfaces\UserGoldFlowRepository;
use App\Repositories\Interfaces\UserRepository;

//弹幕服务
class BarrageService
{
    private $userRepository;
    private $userConfigRepository;

    private $baseBarrageTypeRepository;
    private $barrageTransactionOrderRepository;
    private $userGoldFlowRepository;
    private $liveRoomRepository;
    private $liveBarrageStatisticsRepository;

    public function __construct(
        UserRepository $userRepository,
        UserConfigRepository $userConfigRepository,
        BaseBarrageTypeRepository $baseBarrageTypeRepository,
        BarrageTransactionOrderRepository $barrageTransactionOrderRepository,
        UserGoldFlowRepository $userGoldFlowRepository,
        LiveRoomRepository $liveRoomRepository,
        LiveBarrageStatisticsRepository $liveBarrageStatisticsRepository) {
        $this->userRepository = $userRepository;
        $this->userConfigRepository = $userConfigRepository;
        $this->baseBarrageTypeRepository = $baseBarrageTypeRepository;
        $this->barrageTransactionOrderRepository = $barrageTransactionOrderRepository;
        $this->userGoldFlowRepository = $userGoldFlowRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->liveBarrageStatisticsRepository = $liveBarrageStatisticsRepository;
    }
    /**
     * 依照 id 來取得彈幕資料
     */
    public function findById($barrageId)
    {
        return $this->baseBarrageTypeRepository->findByField('id', $barrageId);
    }

    /**
     * 取得所有彈幕資料
     *
     * @return Collection
     */
    public function findAll()
    {
        return $this->baseBarrageTypeRepository->all();
    }

    /**
     * 取得彈幕列表
     *
     * @return array
     */
    function list() {
        $result = array();
        $collections = $this->baseBarrageTypeRepository->all();

        // 將DB資料整理成API輸出格式
        foreach ($collections as $collection) {
            // 若設定為不顯示, 不輸出
            if ($collection['onshow'] == false) {
                continue;
            }

            $data = array();
            $data['barrage_id'] = $collection['id'];
            $data['brrage_name'] = $collection['name'];
            $data['comment'] = $collection['comment'];
            $data['price'] = $collection['gold_price'];
            array_push($result, $data);
        }
        return $result;
    }

    /**
     * 設定傳送門價格
     *
     * @param double $price
     */
    public function setTransportPrice($price)
    {
        $this->baseBarrageTypeRepository->update(['gold_price' => $price], BaseBarrageTypeModel::TRANSPORT_ID);
    }

    /**
     * 設定彈幕價格
     *
     * @param double $price
     */
    public function setBarragePrice($price)
    {
        $this->baseBarrageTypeRepository->update(['gold_price' => $price], BaseBarrageTypeModel::BARRAGE_ID);
    }

    /**
     * 更新一筆彈幕資料
     */
    public function update($parameters)
    {
        $record = $this->findById($parameters['id']);
        if ($record->count() == 0) {
            throw new \Exception('invalid barrage id');
        }

        $record = $record->first();
        $record->name = $parameters['name'];
        $record->gold_price = $parameters['gold_price'];
        $record->comment = $parameters['comment'];
        $record->onshow = $parameters['onshow'];
        $record->save();
    }

    public function purchaseJob($userModel, $barrageModel, $roomModel, $message, $cacheKey, $fieldKey)
    {
        $userModel = $this->userRepository->findWhere(['id' => $userModel->id])->first();

        $userGoldRemain = $userModel->gold - $barrageModel->gold_price;

        // 寫入交易紀錄
        $order = [
            'room_id' => $roomModel->id,
            'user_id' => $userModel->id,
            'barrage_type_id' => $barrageModel->id,
            'message' => $message,
            'gold_remain' => $userGoldRemain,
        ];
        $barrageCreateMode = $this->barrageTransactionOrderRepository->create($order);

        // 更新用戶金幣數量
        $this->userRepository->addGold($userModel, -1 * $barrageModel->gold_price, $barrageCreateMode);

        // 移除金幣 cache
        $redis = \Cache::store('redis')->getRedis();
        $redis->hdel($cacheKey, $fieldKey);

        // 累計彈幕次數
        $liveBarrageStatisticsModel = $this->liveBarrageStatisticsRepository->findWhere(
            [
                'room_id' => $roomModel->id,
                'barrage_id' => $barrageModel->id,
            ]
        )->first();
        if ($liveBarrageStatisticsModel == null) {
            $data = [
                'room_id' => $roomModel->id,
                'anchor_id' => $roomModel->user_id,
                'barrage_id' => $barrageModel->id,
                'barrage_name' => $barrageModel->name,
                'barrage_price' => $barrageModel->gold_price,
                'count' => 1,
            ];
            $this->liveBarrageStatisticsRepository->create($data);
        } else {
            $liveBarrageStatisticsModel->count += 1;
            $liveBarrageStatisticsModel->save();
        }

    }

    /**
     * 購買彈幕
     *
     * @param int $userId
     * @param int $barrageId
     * @param int $roomId
     * @param string $message
     *
     * @return float
     */
    public function purchase($userId, $barrageId, $roomId, $message)
    {
        // 需要做的驗證
        // 1.room_id是否正在開播
        // 2.barrage_id是否正確
        // 3.用戶金幣是否足夠

        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();
        // 用戶 id 不存在, 回傳error
        if ($userModel == null) {
            throw new \Exception(__('barrage.notFoundUser'));
        }
        $userConfigModel = $this->userConfigRepository->findWhere(['user_id' => $userId])->first();

        switch ($barrageId) {
            case $this->baseBarrageTypeRepository->makeModel()::BARRAGE_ID:
                if ($userConfigModel->can_use_barrage == $userModel->userConfig::CAN_USE_BARRAGE_NO) {
                    throw new \Exception(__('barrage.hasForbiddenPurchaseBarrage'));
                }
                break;
            case $this->baseBarrageTypeRepository->makeModel()::TRANSPORT_ID:
                if ($userConfigModel->can_use_transfer == $userModel->userConfig::CAN_USE_TRANSFER_NO) {
                    throw new \Exception(__('barrage.hasForbiddenPurchaseTransport'));
                }
                break;
            default:
                break;
        }

        // 從DB取出直播間資料
        $roomCollection = $this->liveRoomRepository->findByField('id', $roomId);
        $roomModel = $roomCollection->first();

        // 直播間不存在, 回傳error
        if ($roomModel == null) {
            throw new \Exception(__('barrage.notFoundLiveRoom'));
        }

        // 直播間狀態不是開播中, 回傳error
        if ($roomModel->status != LiveRoomModel::STATUS_LIVE) {
            throw new \Exception(__('barrage.roomNotLive'));
        }

        // 從DB取出彈幕資料
        $barrageCollection = $this->baseBarrageTypeRepository->findByField('id', $barrageId);
        $barrageModel = $barrageCollection->first();

        // 彈幕id不存在, 回傳error
        if ($barrageModel == null) {
            throw new \Exception(__('barrage.barrageIdNotFound'));
        }

        // 用戶金幣不夠, 回傳 USER_GOLD_NOT_ENOUGH 狀態
        $currentGold = $userModel->gold_cache;
        if ($currentGold < $barrageModel->gold_price) {
            throw new \Exception(__('user.gold_not_enough'), ErrorCode::USER_GOLD_NOT_ENOUGH);
        }

        $gold = -1 * $barrageModel->gold_price;
        $userGold = $currentGold - $barrageModel->gold_price;
        $this->userRepository->addGold($userModel, $gold, null, true, function ($cacheKey, $fieldKey) use ($userModel, $barrageModel, $roomModel, $message) {
            \Queue::pushOn(pool('barrage'), new \App\Jobs\SendBarrage($userModel, $barrageModel, $roomModel, $message, $cacheKey, $fieldKey));
        });

        // 將傳送門訊息發送到IM大群
        if ($this->baseBarrageTypeRepository->makeModel()::TRANSPORT_ID == $barrageId) {
            // 取得主播資料
            $anchorId = $roomModel->user_id;
            $anchorModel = $this->userRepository->findWhere(['id' => $anchorId])->first();

            // 發送訊息到im - 資料準備
            $broadCastData = [
                'GroupId' => 'Common',
                'MsgBody' => [
                    [
                        'MsgType' => 'TIMCustomElem',
                    ],
                ],
            ];
            $imData = [
                'content' => $message,

                'isAnchor' => true,
                'roomID' => (string) $roomModel->id,

                'pusherID' => (string) $anchorId,
                'pusherNickName' => $anchorModel->nickname,
                'pusherPic' => $anchorModel->avatar,

                'senderID' => (string) $userId,
                'senderLevel' => (string) $userModel->current_level,
                'senderName' => $userModel->nickname,
                'senderPic' => $userModel->avatar,
            ];
            $jsonData = json_encode($imData);
            $msgData = batchReplaceLocaleByArray(
                'im_message.' . app('im')::MESSAGE_TYPE_108,
                ['data' => $jsonData]
            );
            $msgData['isAnchor'] = true;
            $msgData['level'] = $userModel->current_level;
            $msgData['userAvatar'] = $userModel->avatar;
            $msgData['userName'] = $userModel->nickname;
            // 發送訊息到im - 資料準備
            $result = \IM::sendBroadCast($broadCastData, [['msg' => $msgData]]);
        }
        return $userGold;
    }
}
