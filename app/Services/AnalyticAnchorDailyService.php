<?php
namespace App\Services;

use App\Models\BaseBarrageType as BaseBarrageTypeModel;
use App\Models\GameBetRecord as GameBetRecordModel;
use App\Models\User as UserModel;
use App\Repositories\Interfaces\AnalyticDailyAnchorNumberReportRepository;
use App\Repositories\Interfaces\AnalyticDailyGameIncomeReportRepository;
use App\Repositories\Interfaces\AnalyticDailyLiveIncomeReportRepository;
use App\Repositories\Interfaces\BarrageTransactionOrderRepository;
use App\Repositories\Interfaces\BaseBarrageTypeRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use App\Repositories\Interfaces\GameBetRecordRepository;
use App\Repositories\Interfaces\GameRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;
use App\Repositories\Interfaces\LiveRoomRepository;
use App\Repositories\Interfaces\UserLoginRecordRepository;
use App\Repositories\Interfaces\UserRepository;
use Carbon\Carbon;

//每日主播报表服务
class AnalyticAnchorDailyService
{
    protected $userRepository;
    protected $liveRoomRepository;
    protected $userLoginRecordRepository;
    protected $gameRepository;
    protected $gameBetRecordRepository;
    protected $giftTransactionOrderRepository;
    protected $barrageTransactionOrderRepository;
    protected $baseGiftTypeRepository;
    protected $baseBarrageTypeRepository;

    protected $analyticDailyAnchorNumberReportRepository;
    protected $analyticDailyGameIncomeReportRepository;
    protected $analyticDailyLiveIncomeReportRepository;

    public function __construct(
        UserRepository $userRepository,
        LiveRoomRepository $liveRoomRepository,
        UserLoginRecordRepository $userLoginRecordRepository,
        GameRepository $gameRepository,
        GameBetRecordRepository $gameBetRecordRepository,
        GiftTransactionOrderRepository $giftTransactionOrderRepository,
        BarrageTransactionOrderRepository $barrageTransactionOrderRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        BaseBarrageTypeRepository $baseBarrageTypeRepository,
        AnalyticDailyAnchorNumberReportRepository $analyticDailyAnchorNumberReportRepository,
        AnalyticDailyGameIncomeReportRepository $analyticDailyGameIncomeReportRepository,
        AnalyticDailyLiveIncomeReportRepository $analyticDailyLiveIncomeReportRepository
    ) {
        $this->userRepository = $userRepository;
        $this->liveRoomRepository = $liveRoomRepository;
        $this->userLoginRecordRepository = $userLoginRecordRepository;
        $this->gameRepository = $gameRepository;
        $this->gameBetRecordRepository = $gameBetRecordRepository;
        $this->giftTransactionOrderRepository = $giftTransactionOrderRepository;
        $this->barrageTransactionOrderRepository = $barrageTransactionOrderRepository;
        $this->baseGiftTypeRepository = $baseGiftTypeRepository;
        $this->baseBarrageTypeRepository = $baseBarrageTypeRepository;

        $this->analyticDailyAnchorNumberReportRepository = $analyticDailyAnchorNumberReportRepository;
        $this->analyticDailyGameIncomeReportRepository = $analyticDailyGameIncomeReportRepository;
        $this->analyticDailyLiveIncomeReportRepository = $analyticDailyLiveIncomeReportRepository;
    }

    /**
     * 製作每日直播收益資料
     */
    public function makeDailyLiveIncome($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }

        $date = Carbon::now()->format('Y-m-d');

        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        $result = [
            'date' => $date,
            'total_recieve_gold' => 0,
            'average_recieve_gold' => 0,
            'purchase_prop_times' => 0,
            'prop_incom' => 0,
            'purchase_transport_times' => 0,
            'transport_incom' => 0,
        ];

        // 所有禮物基本資訊
        $allGiftModelArray = $this->baseGiftTypeRepository->all()->keyby('type_slug');

        // 傳送門價格
        $transportGoldPrice = $this->baseBarrageTypeRepository->findWhere(
            ['id' => BaseBarrageTypeModel::TRANSPORT_ID]
        )->first()->gold_price;

        // 當日開直播次數
        $allGameLiveModelArray = $this->liveRoomRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();
        $allLiveNumber = count($allGameLiveModelArray);
        if ($allLiveNumber == 0) {
            $this->analyticDailyLiveIncomeReportRepository->create($result);
            return;
        }

        // 取得當日禮物交易紀錄
        $allGiftOrderModelArray = $this->giftTransactionOrderRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();

        // 取得當日傳送門交易紀錄
        $allBarrageOrderModelArray = $this->barrageTransactionOrderRepository->findWhere(
            [
                'barrage_type_id' => BaseBarrageTypeModel::TRANSPORT_ID,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();

        $totalReceiveGold = 0;
        $purchasePropTimes = 0;
        $purchasePropIncome = 0;
        $length = count($allGiftOrderModelArray);
        for ($i = 0; $i < $length; $i++) {
            $giftOrderModel = $allGiftOrderModelArray[$i];

            // 累加總收禮金幣
            $totalReceiveGold += $giftOrderModel->gold_price;

            // 計算購買道具次數 & 道具收益
            if (isset($allGiftModelArray[$giftOrderModel->gift_type_id])) {
                $giftModel = $allGiftModelArray[$giftOrderModel->gift_type_id];
                if ($giftModel->is_prop) {
                    $purchasePropTimes++;
                    $purchasePropIncome += $giftOrderModel->gold_price;
                }
            }
        }

        $result['total_recieve_gold'] = $totalReceiveGold;
        $result['average_recieve_gold'] = $totalReceiveGold / (float) $allLiveNumber;
        $result['purchase_prop_times'] = $purchasePropTimes;
        $result['prop_incom'] = $purchasePropIncome;

        // 累加傳送門的收益
        $purchaseTransportTimes = count($allBarrageOrderModelArray);
        $totalTransportIncome = 0;
        for ($i = 0; $i < $purchaseTransportTimes; $i++) {
            $barrageOrderModel = $allBarrageOrderModelArray[$i];
            $totalTransportIncome += $transportGoldPrice;
        }
        $result['purchase_transport_times'] = $purchaseTransportTimes;
        $result['transport_incom'] = $totalTransportIncome;

        // 資料寫入db
        $record = $this->analyticDailyLiveIncomeReportRepository->findWhere([
            'date' => $date,
        ]
        )->first();
        if ($record) {
            $record->update($result);
        } else {
            $this->analyticDailyLiveIncomeReportRepository->create($result);
        }
    }

    /**
     * 製作每日遊戲收益數據
     */
    public function makeDailyGameIncome($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        $allGameModelArray = $this->gameRepository->all()->keyby('slug');
        $allGameBetRecordModelArray = $this->gameBetRecordRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();

        $totalGameSlug = [];
        $gameToBetPeopleNumber = [];
        $gameToBetNumber = [];
        $gameToBetGold = [];
        $gameToWinNumber = [];
        $gameToWinGold = [];

        $length = count($allGameBetRecordModelArray);
        //先一次把所有当天的游戏记录拉出来
        $recordList = $this->analyticDailyGameIncomeReportRepository->skipCache()->findWhere([
            'date' => $date,
        ]
        )->keyBy('game_slug');

        foreach ($allGameModelArray as $game_slug => $game) {
            if (!isset($recordList[$game_slug])) {
                //表示还没建资料物件出来, 先把预设的资料填进去
                $recordList->put($game_slug, $this->insertEmptyGameRecord($game, $date));
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $gameBetRecordModel = $allGameBetRecordModelArray[$i];
            $gameSlug = $gameBetRecordModel->game_slug;
            if ($gameSlug == '' || $gameSlug == null) {
                continue;
            }
            if (!in_array($gameSlug, $totalGameSlug)) {
                $totalGameSlug[] = $gameSlug;
            }

            // 增加該遊戲的總下注次數
            if (isset($gameToBetNumber[$gameSlug])) {
                $gameToBetNumber[$gameSlug]++;
            } else {
                $gameToBetNumber[$gameSlug] = 1;
            }

            // 增加該遊戲的下注玩家數
            $gameToBetPeopleNumber[$gameSlug][$gameBetRecordModel->user_id] = 1;

            // 增加該遊戲的下注金幣
            if (!isset($gameToBetGold[$gameSlug])) {
                $gameToBetGold[$gameSlug] = $gameBetRecordModel->bet_gold;
            } else {
                $gameToBetGold[$gameSlug] += $gameBetRecordModel->bet_gold;
            }

            // 增加該遊戲的得獎次數
            if ($gameBetRecordModel->status == GameBetRecordModel::STATUS_WIN) {
                if (!isset($gameToWinNumber[$gameSlug])) {
                    $gameToWinNumber[$gameSlug] = 1;
                } else {
                    $gameToWinNumber[$gameSlug]++;
                }
            }
            // 增加該遊戲的得獎金幣
            if (isset($gameToWinGold[$gameSlug])) {
                $gameToWinGold[$gameSlug] += $gameBetRecordModel->win_gold;
            } else {
                $gameToWinGold[$gameSlug] = $gameBetRecordModel->win_gold;
            }

        }
        $gameSlugLength = count($totalGameSlug);
        //要更新的记录资料列表
        $dataList = [];
        for ($i = 0; $i < $gameSlugLength; $i++) {
            $data = [];
            $slug = $totalGameSlug[$i];

            // 該遊戲當日開直播次數
            $allGameLiveModelArray = $this->liveRoomRepository->findWhere(
                [
                    'game_slug' => $slug,
                    ['created_at', '>=', $startDatetime],
                    ['created_at', '<=', $endDatetime],
                ]
            )->all();
            $allLiveNumber = count($allGameLiveModelArray);

            $data['date'] = $date;

            if (isset($allGameModelArray[$gameSlug])) {
                $data['game_name'] = $allGameModelArray[$gameSlug]->name;
            } else {
                $data['game_name'] = '';
            }

            $data['game_slug'] = $gameSlug;

            $data['bet_people_number'] = count($gameToBetPeopleNumber[$slug]);

            $data['bet_number'] = $gameToBetNumber[$slug];
            if ($allLiveNumber == 0) {
                $data['average_bet_number_per_round'] = 0;
            } else {
                $data['average_bet_number_per_round'] = $data['bet_number'] / (float) $allLiveNumber;
            }

            $data['bet_gold'] = $gameToBetGold[$slug];

            if ($allLiveNumber == 0) {
                $data['average_bet_gold_per_round'] = 0;
            } else {
                $data['average_bet_gold_per_round'] = $data['bet_gold'] / (float) $allLiveNumber;
            }

            if (isset($gameToWinNumber[$slug])) {
                $data['win_number'] = $gameToWinNumber[$slug];
                if ($allLiveNumber == 0) {
                    $data['average_win_number_per_round'] = 0;
                } else {
                    $data['average_win_number_per_round'] = $data['win_number'] / (float) $allLiveNumber;
                }

            } else {
                $data['win_number'] = 0;
                $data['average_win_number_per_round'] = 0;
            }

            $data['win_gold'] = $gameToWinGold[$slug];
            if ($allLiveNumber == 0) {
                $data['average_win_gold_per_round'] = 0;
            } else {
                $data['average_win_gold_per_round'] = $data['win_gold'] / (float) $allLiveNumber;
            }

            $data['profit'] = $data['bet_gold'] - $data['win_gold'];
            $dataList[$slug] = $data;
        }
        //在这里一次储存
        $recordList->map(function ($record) use ($dataList) {
            //这里有一个盲点，如果 dataList 有 出现 没有在游戏列表的游戏记录，就不会记录
            $updateData = $dataList[$record->game_slug] ?? [];
            if ($updateData) {
                foreach ($updateData as $column => $val) {
                    $record->$column = $val;
                }
            }
            $record->save();
        });
    }

    // private  function fillDataToAnalyticDailyGameIncomeReport()
    /**
     * 新建一笔空白 analyticDailyGameIncomeReport
     *
     * @param    [type]                   $game [description]
     * @param    [type]                   $date [description]
     *
     * @return   AnalyticDailyGameIncomeReport                         营收记录
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-14T13:27:57+0800
     */
    private function insertEmptyGameRecord($game, $date)
    {
        $record = $this->analyticDailyGameIncomeReportRepository->makeModel();
        $record->date = $date;
        $record->game_slug = $game->slug;
        $record->game_name = $game->name;
        $record->bet_people_number = 0;
        $record->bet_number = 0;
        $record->average_bet_number_per_round = 0;
        $record->bet_gold = 0;
        $record->average_bet_gold_per_round = 0;
        $record->win_number = 0;
        $record->average_win_number_per_round = 0;
        $record->win_gold = 0;
        $record->average_win_gold_per_round = 0;
        $record->profit = 0;
        return $record;
    }

    /**
     * 製作主播數量相關統計資料
     */
    public function makeDailyAnchorNumberReport($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $result = [
            'date' => $date,
            'new_register_anchor_number' => '',
            'all_anchor_number' => '',
            'live_anchor_number' => '',
            'live_number' => '',
            'audience_number' => '',
            'average_live_time' => '',
        ];

        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        // 計算新註冊主播數
        $allNewRegisterAnchorModelArray = $this->userRepository->findWhere(
            [
                'user_type_id' => UserModel::USER_TYPE_ANCHOR,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();
        $result['new_register_anchor_number'] = count($allNewRegisterAnchorModelArray);

        // 計算全部主播數
        $allAnchorModelArray = $this->userRepository->findWhere(
            [
                'user_type_id' => UserModel::USER_TYPE_ANCHOR,
            ]
        )->all();
        $result['all_anchor_number'] = count($allAnchorModelArray);

        // 計算開直播次數
        $allLiveModelArray = $this->liveRoomRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();
        $result['live_number'] = count($allLiveModelArray);

        // 計算開直播主播數
        $allLiveAnchorArray = [];
        $length = count($allLiveModelArray);
        for ($i = 0; $i < $length; $i++) {
            $anchorId = $allLiveModelArray[$i]['user_id'];
            $allLiveAnchorArray[$anchorId] = 1;
        }
        $result['live_anchor_number'] = count($allLiveAnchorArray);

        // 計算平均開播時間
        $totalLiveTime = 0;
        $duration = 0;
        for ($i = 0; $i < $length; $i++) {
            $startLiveTime = Carbon::parse($allLiveModelArray[$i]['created_at'])->timestamp;
            $endLiveTime = Carbon::parse($endDatetime)->timestamp;
            if ($allLiveModelArray[$i]['leave_at'] != null) {
                $endLiveTime = Carbon::parse($allLiveModelArray[$i]['leave_at'])->timestamp;
            }
            $duration = $endLiveTime - $startLiveTime;

            $totalLiveTime += $duration;
        }
        if ($length == 0) {
            $result['average_live_time'] = 0;
        } else {
            $result['average_live_time'] = round($duration / (float) $length);
        }

        $allLoginRecordModelArray = $this->userLoginRecordRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->all();

        $allLoginUser = [];
        $length = count($allLoginRecordModelArray);
        for ($i = 0; $i < $length; $i++) {
            $anchorId = $allLoginRecordModelArray[$i]['user_id'];
            $allLiveAnchorArray[$anchorId] = 1;
        }
        $result['audience_number'] = count($allLoginRecordModelArray);

        // 資料寫入db
        $record = $this->analyticDailyAnchorNumberReportRepository->findWhere(['date' => $date])->first();
        if ($record) {
            $record->update($result);
        } else {
            $this->analyticDailyAnchorNumberReportRepository->create($result);
        }
    }

}
