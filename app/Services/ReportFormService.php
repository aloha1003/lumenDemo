<?php
namespace App\Services;

use App\Repositories\Interfaces\AnalyticDailyGameIncomeReportRepository;
use App\Repositories\Interfaces\DailyGameBetReportRepository;
use App\Repositories\Interfaces\DailyRevenueRepository;
use App\Repositories\Interfaces\DailyUserReportRepository;
use App\Repositories\Interfaces\DailyWithdrawReportRepository;
use App\Repositories\Interfaces\GameBetRecordRepository;
use App\Repositories\Interfaces\UserLoginRecordRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Repositories\Interfaces\PaymentChannelRepository;
use App\Repositories\Interfaces\UserTopupOrderRepository;
use App\Repositories\Interfaces\WithDrawGoldApplyRepository;
use App\Repositories\Interfaces\CompanyWithdrawRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\AgentTransactionListRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;
use App\Repositories\Interfaces\BarrageTransactionOrderRepository;
use App\Repositories\Interfaces\BaseBarrageTypeRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;

use App\Repositories\Interfaces\AnalyticDailyUserNumberReportRepository;
use App\Repositories\Interfaces\AnalyticDailyChannelTopupRepository;
use App\Repositories\Interfaces\AnalyticDailyAdminTopupApplyReportRepository;
use App\Repositories\Interfaces\AnalyticDailyUserWithdrawReportRepository;
use App\Repositories\Interfaces\AnalyticDailyCompanyWithdrawReportRepository;
use App\Repositories\Interfaces\AnalyticGoldStatisticsReportRepository;

use App\Repositories\Interfaces\GoldTopupApplicationRepository;
use App\Models\GoldTopupApplication;
use App\Models\WithDrawGoldApply;
use App\Models\CompanyWithdraw;
use Carbon\Carbon;

/**
 * 报表服务
 */
class ReportFormService
{
    use \App\Traits\MagicGetTrait;
    protected $userRepository;
    protected $dailyUserReportRepository;
    protected $paymentChannelRepository;
    protected $userTopupOrderRepository;
    protected $userLoginRecordRepository;
    protected $dailyRevenueRepository;
    protected $dailyWithdrawReportRepository;
    protected $companyWithdrawRepository;
    protected $managerRepository;

    protected $withDrawGoldApplyRepository;
    protected $gameBetRecordRepository;
    protected $baseGiftTypeRepository;
    protected $baseBarrageTypeRepository;
    protected $giftTransactionOrderRepository;
    protected $barrageTransactionOrderRepository;
    protected $dailyGameBetReportRepository;
    protected $analyticDailyGameIncomeReportRepository;
    protected $analyticDailyUserNumberReportRepository;
    protected $analyticDailyChannelTopupRepository;
    protected $analyticDailyUserWithdrawReportRepository;
    protected $analyticDailyCompanyWithdrawReportRepository;
    protected $analyticDailyAdminTopupApplyReportRepository;
    protected $analyticGoldStatisticsReportRepository;
    protected $agentTransactionListRepository;
    protected $goldTopupApplicationRepository;

    public function __construct(DailyUserReportRepository $dailyUserReportRepository,
        UserRepository $userRepository,
        PaymentChannelRepository $paymentChannelRepository,
        UserTopupOrderRepository $userTopupOrderRepository,
        UserLoginRecordRepository $userLoginRecordRepository,
        DailyRevenueRepository $dailyRevenueRepository,
        DailyWithdrawReportRepository $dailyWithdrawReportRepository,
        CompanyWithdrawRepository $companyWithdrawRepository,
        ManagerRepository $managerRepository,
        WithDrawGoldApplyRepository $withDrawGoldApplyRepository,
        GameBetRecordRepository $gameBetRecordRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        BaseBarrageTypeRepository $baseBarrageTypeRepository,

        GiftTransactionOrderRepository $giftTransactionOrderRepository,
        BarrageTransactionOrderRepository $barrageTransactionOrderRepository,
        DailyGameBetReportRepository $dailyGameBetReportRepository,
        AnalyticDailyGameIncomeReportRepository $analyticDailyGameIncomeReportRepository,
        AnalyticDailyUserNumberReportRepository $analyticDailyUserNumberReportRepository,
        AnalyticDailyCompanyWithdrawReportRepository $analyticDailyCompanyWithdrawReportRepository,
        AnalyticDailyChannelTopupRepository $analyticDailyChannelTopupRepository,
        AnalyticDailyAdminTopupApplyReportRepository $analyticDailyAdminTopupApplyReportRepository,
        AnalyticDailyUserWithdrawReportRepository $analyticDailyUserWithdrawReportRepository,
        GoldTopupApplicationRepository $goldTopupApplicationRepository,
        AnalyticGoldStatisticsReportRepository $analyticGoldStatisticsReportRepository,
        AgentTransactionListRepository $agentTransactionListRepository
    ) {
        $this->userRepository = $userRepository;
        $this->paymentChannelRepository = $paymentChannelRepository;
        $this->dailyUserReportRepository = $dailyUserReportRepository;
        $this->userTopupOrderRepository = $userTopupOrderRepository;
        $this->userLoginRecordRepository = $userLoginRecordRepository;
        $this->dailyRevenueRepository = $dailyRevenueRepository;
        $this->dailyWithdrawReportRepository = $dailyWithdrawReportRepository;
        $this->companyWithdrawRepository = $companyWithdrawRepository;
        $this->managerRepository = $managerRepository;
        $this->withDrawGoldApplyRepository = $withDrawGoldApplyRepository;
        $this->gameBetRecordRepository = $gameBetRecordRepository;
        $this->giftTransactionOrderRepository = $giftTransactionOrderRepository;
        $this->barrageTransactionOrderRepository = $barrageTransactionOrderRepository;
        $this->baseGiftTypeRepository = $baseGiftTypeRepository;
        $this->baseBarrageTypeRepository = $baseBarrageTypeRepository;
        $this->dailyGameBetReportRepository = $dailyGameBetReportRepository;
        $this->analyticDailyGameIncomeReportRepository = $analyticDailyGameIncomeReportRepository;
        $this->analyticDailyUserNumberReportRepository = $analyticDailyUserNumberReportRepository;
        $this->analyticDailyChannelTopupRepository = $analyticDailyChannelTopupRepository;

        $this->analyticDailyAdminTopupApplyReportRepository = $analyticDailyAdminTopupApplyReportRepository;
        $this->analyticDailyUserWithdrawReportRepository = $analyticDailyUserWithdrawReportRepository;
        $this->analyticDailyCompanyWithdrawReportRepository = $analyticDailyCompanyWithdrawReportRepository;
        $this->goldTopupApplicationRepository = $goldTopupApplicationRepository;
        $this->analyticGoldStatisticsReportRepository = $analyticGoldStatisticsReportRepository;
        $this->agentTransactionListRepository = $agentTransactionListRepository;

    }

    /**
     * 記錄每日人數統計
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-07T09:15:06+0800
     */
    public function setDailyLoginReportForm()
    {
        $time = time();
        $nowDate = date("Y-m-d", $time);
        $nowHour = date("H", $time);
        //检查当天是否记录了
        $currentRecord = $this->dailyUserReportRepository->findWhere(['date' => $nowDate])->first();
        if (!$currentRecord) {
            $currentRecord = $this->dailyUserReportRepository->makeModel();
        }
        $currentRecord->date = $nowDate;
        //透过IM 取得当前的在人线人数
        $onlineCount = \IM::getCurrentUserCount();
        $hourAttrName = $currentRecord->getHourColumn($nowHour);
        $currentRecord->$hourAttrName = $onlineCount;
        //取得登入人数
        $loginUserCount = $this->userRepository->makeModel()->loginAccount($nowDate);
        $currentRecord->login_user_count = $loginUserCount;
        //新帐号
        $newUserCount = $this->userRepository
            ->makeModel()
            ->where([[\DB::raw('date(created_at)'), '=', $nowDate]])
            ->select(\DB::raw('count(1) as count'))
            ->get()
            ->first()
            ->only('count')
        ;
        $currentRecord->new_users_count = $newUserCount['count'];
        $currentRecord->save();
    }

    /**
     * 記錄每日營收
     *
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-07T09:15:59+0800
     */
    public function setDailyRevenueReportForm($nowDate)
    {
        //取得当天有登入的帐号
        $userWhere = [
            [\DB::raw('date(created_at)'), '=', $nowDate],
        ];
        $result = $this->userLoginRecordRepository->makeModel()
            ->where($userWhere)
            ->select(\DB::raw('date(created_at) as date'), \DB::raw('count(DISTINCT user_id) as count'))
            ->groupBy('date')
            ->get()
            ->keyBy('date')->toArray();
        $loginUserCount = $result[$nowDate]['count'] ?? 0;

        //取得当天有充值的记录
        $topupOrderModel = $this->userTopupOrderRepository
            ->makeModel();
        $where = [
            [\DB::raw('date(pay_at)'), '=', $nowDate],
            ['pay_step', '=', $topupOrderModel::PAY_STEP_SUCCESS],
        ];

        $report = $this->userTopupOrderRepository
            ->makeModel()
            ->where($where)
            ->select(\DB::raw('date(pay_at) as date'), \DB::raw('count(DISTINCT user_id) as user_id_count'), \DB::raw('sum(amount) as amount_sum'), \DB::raw('avg(amount) as amount_avg'), \DB::raw('count(1) as topup_times'))
            ->groupBy('date')
            ->get()
            ->keyBy('date')
            ->map(function ($item) use ($loginUserCount) {
                if ($loginUserCount == 0) {
                    $item->pay_rate = 0;
                } else {
                    $item->pay_rate = round($item->user_id_count / $loginUserCount, 2) * 100;
                }

                return $item;
            })
            ->first()
        ;
        //取得今天的记录
        $dailyRevenueRecord = $this->dailyRevenueRepository->findWhere(['date' => $nowDate])->first();
        if (!$dailyRevenueRecord) {
            $dailyRevenueRecord = $this->dailyRevenueRepository->makeModel();
        }
        //写入记录
        if (!$report) {
            $dailyRevenueRecord->date = $nowDate;
            $dailyRevenueRecord->user_id_count = 0;
            $dailyRevenueRecord->topup_times = 0;
            $dailyRevenueRecord->amount_sum = 0;
            $dailyRevenueRecord->amount_avg = 0;
            $dailyRevenueRecord->login_user_count = 0;
            $dailyRevenueRecord->pay_rate = 0;
        } else {
            $dailyRevenueRecord->date = $nowDate;
            $dailyRevenueRecord->user_id_count = $report->user_id_count;
            $dailyRevenueRecord->topup_times = $report->topup_times;
            $dailyRevenueRecord->amount_sum = $report->amount_sum;
            $dailyRevenueRecord->amount_avg = $report->amount_avg;
            $dailyRevenueRecord->login_user_count = $loginUserCount;
            $dailyRevenueRecord->pay_rate = $report->pay_rate;
        }
        $dailyRevenueRecord->save();
    }

    /**
     * 取得每日登入帐号数, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyLoginAccounts($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyUserReportRepository
            ->findWhere($where, ['login_user_count', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 取得每日登入帐号数, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyNewAccounts($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyUserReportRepository
            ->findWhere($where, ['new_users_count', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 统一处理时间
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T15:43:11+0800
     */
    private function getBetweenRule($filter = [])
    {
        if (!$filter) {
            $start = date("Y-m-d", strtotime('-7 day'));
            $end = date("Y-m-d", time());

        } else {
            $start = $filter['start'] ?? date("Y-m-d", strtotime('-7 day'));
            $end = $filter['end'] ?? date("Y-m-d", time());
        }
        return compact('start', 'end');
    }

    /**
     * 取得每日充值次数, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyTopupTimes($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyRevenueRepository
            ->findWhere($where, ['topup_times', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 取得每日充值次数, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyTopupAmounts($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyRevenueRepository
            ->findWhere($where, ['amount_sum', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 写每日提现报表
     *
     * @param    [type]                   $nowDate [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T17:00:11+0800
     */
    public function setDailyWithdrawReportForm($nowDate)
    {

        //取得当天有提现
        $withDrawGoldApplyModel = $this->withDrawGoldApplyRepository
            ->makeModel();
        $where = [
            [\DB::raw('date(updated_at)'), '=', $nowDate],
            ['status', '=', $withDrawGoldApplyModel::STATUS_PASS],
        ];

        $report = $this->withDrawGoldApplyRepository
            ->makeModel()
            ->where($where)
            ->select(\DB::raw('date(updated_at) as date'), \DB::raw('count(DISTINCT user_id) as user_id_count'), \DB::raw('sum(gold) as gold_sum'), \DB::raw('avg(gold) as gold_avg'), \DB::raw('count(1) as times'))
            ->groupBy('date')
            ->get()
            ->keyBy('date')
            ->first()
        ;
        //取得今天的记录
        $dailyWithdrawReport = $this->dailyWithdrawReportRepository->findWhere(['date' => $nowDate])->first();
        if (!$dailyWithdrawReport) {
            $dailyWithdrawReport = $this->dailyWithdrawReportRepository->makeModel();
        }
        //写入记录
        if (!$report) {
            $dailyWithdrawReport->date = $nowDate;
            $dailyWithdrawReport->user_id_count = 0;
            $dailyWithdrawReport->times = 0;
            $dailyWithdrawReport->gold_sum = 0;
            $dailyWithdrawReport->gold_avg = 0;
        } else {
            $dailyWithdrawReport->date = $nowDate;
            $dailyWithdrawReport->user_id_count = $report->user_id_count;
            $dailyWithdrawReport->times = $report->times;
            $dailyWithdrawReport->gold_sum = goldToCoin($report->gold_sum);
            $dailyWithdrawReport->gold_avg = goldToCoin($report->gold_avg);
        }
        $dailyWithdrawReport->save();
    }

    /**
     * 取得每日提现金额, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyWithdrawGolds($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyWithdrawReportRepository
            ->findWhere($where, ['gold_sum', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 取得每日提现次数, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyWithdrawTimes($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->dailyWithdrawReportRepository
            ->findWhere($where, ['times', 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 写每日游戏下注报表
     *
     * @param    [type]                   $nowDate [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T17:00:11+0800
     */
    public function setDailyGameBetReportForm($nowDate)
    {

        $where = [
            [\DB::raw('date(updated_at)'), '=', $nowDate],
        ];

        $report = $this->gameBetRecordRepository
            ->makeModel()
            ->where($where)
            ->select(\DB::raw('date(updated_at) as date'), \DB::raw('sum(bet_gold) as bet_golds'), \DB::raw('sum(win_gold) as win_golds'), 'game_slug')
            ->groupBy(['date', 'game_slug'])
            ->get()
        ;
        if ($report) {
            foreach ($report as $key => $item) {
                //取得今天的记录
                $record = $this->dailyGameBetReportRepository->findWhere(['date' => $item->date, 'game_slug' => $item->game_slug])->first();
                if (!$record) {
                    $record = $this->dailyGameBetReportRepository->makeModel();
                }
                //写入记录
                if (!$report) {
                    $record->date = $nowDate;
                    $record->bet_golds = 0;
                    $record->win_golds = 0;
                    $record->game_slug = $item['game_slug'];
                } else {
                    $record->date = $nowDate;
                    $record->bet_golds = $item->bet_golds;
                    $record->win_golds = $item->win_golds;
                    $record->game_slug = $item->game_slug;
                }
                $record->save();
            }
        }
    }

    /**
     * 取得每日下注金额, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyBetGolds($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->analyticDailyGameIncomeReportRepository
            ->scopeQuery(function ($query) {
                return $query->groupBy('date');
            })
            ->findWhere($where, [\DB::raw('sum(bet_gold) as bet_godls_sum'), 'date'])
            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;
    }

    /**
     * 取得每日奖金金额, 预设抓七天
     *
     * @param    array                    $filter [description]
     *
     * @return   [type]                           [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-11T14:48:45+0800
     */
    public function getDailyWinGolds($filter = [])
    {
        extract($this->getBetweenRule($filter));
        $where = [
            ['date', 'between', [$start, $end]],
        ];
        $record = $this->analyticDailyGameIncomeReportRepository
            ->scopeQuery(function ($query) {
                return $query->groupBy('date');
            })
            ->findWhere($where, [\DB::raw('sum(win_gold) as win_golds_sum'), 'date'])

            ->map(function ($item) {
                $item->date = date("m-d", strtotime($item->date));
                unset($item->max_hour);
                return $item;
            })
            ->toArray()
        ;
        return $record;

    }

    /**
     * 每日用戶數據
     */
    public function getDailyUserNumber($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';
        $result = [
            'date' => $date,
            'new_register_user_number' => 0,
            'login_user_number' => 0,
            'max_online_user_number' => 0,
            'login_times' => 0,
            'last_30_day_login_user_number' => 0,
            'adhesive' => 0,
        ];
        
        // 取得每日用戶統計資料
        $userReportModel = $this->dailyUserReportRepository->findWhere([
            ['created_at', '>=', $startDatetime],
            ['created_at', '<=', $endDatetime],
        ])->first();

        if ($userReportModel != null) {
            // 註冊用戶數
            $result['new_register_user_number'] = $userReportModel->new_users_count;

            // 登入用戶數
            $result['login_user_number'] = $userReportModel->login_user_count;

            // 最高人數
            $result['max_online_user_number'] = $userReportModel->max_hour;

            // 當日登入次數
            $loginModelArray = $this->userLoginRecordRepository->findWhere([
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],    
            ])->all();
            $loginTimes = count($loginModelArray);
            $result['login_times'] = $loginTimes;

            // 30天不重複登入人數
            $last30Datetime = Carbon::parse($startDatetime)->addDays(-30)->format('Y-m-d H:i:s');
            $where = [
                ['created_at', '=', $last30Datetime],
                ['created_at', '<=', $endDatetime],    
            ];
            $last30LoginNumber = $this->userLoginRecordRepository->model()::select('user_id')->where($where)->distinct('user_id')->count('user_id');
            $result['last_30_day_login_user_number'] = $last30LoginNumber;

            // 黏著度
            if ($result['last_30_day_login_user_number'] != 0 ) {
                $result['adhesive'] = round( ($result['login_user_number'] / (float)$result['last_30_day_login_user_number']) * 100.0) / 100.0 ;
            } else {
                
                $result['adhesive'] = 0;
            }
        }
        // 資料寫入db
        $record = $this->analyticDailyUserNumberReportRepository->findWhere(['date' => $date])->first();
        if ($record) {
            $record->update($result);
        } else {
            $this->analyticDailyUserNumberReportRepository->create($result);
        }        
    }

    /**
     * 每日充值渠道資訊統計
     */
    public function getDailyTopupChannelReport($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        // 取得每日用戶統計資料
        $userReportModel = $this->dailyUserReportRepository->findWhere([
            ['created_at', '>=', $startDatetime],
            ['created_at', '<=', $endDatetime],
        ])->first();
        // 登入用戶數
        $loginUserNumber = $userReportModel->login_user_count;

        // 取出所有充值訂單
        $allOrderModelArray = $this->userTopupOrderRepository->findWhere([
            ['created_at', '>=', $startDatetime],
            ['created_at', '<=', $endDatetime],
        ])->all();
        
        $userPerChannel = [];
        $rmbPerChannel = [];
        $length = count($allOrderModelArray);
        // 計算每個充值渠道的用戶數與人民幣金額
        for ($i=0; $i < $length; $i++) {
            $orderModel = $allOrderModelArray[$i];
            $channelSlug = $orderModel->pay_channel_payments_pay_type;

            $userPerChannel[$channelSlug][$orderModel->user_id] = 1;

            if (!isset($rmbPerChannel[$channelSlug])) {
                $rmbPerChannel[$channelSlug] = $orderModel->amount;
            } else {
                $rmbPerChannel[$channelSlug] += $orderModel->amount;
            }
        }

        $result = [];
        $allPaymentChannelModelArray = $this->paymentChannelRepository->all()->keyby('slug');
        foreach ($allPaymentChannelModelArray as $slug => $paymentChannelModel) {
            $result = [
                'date' => $date,
                'payment_channel_slug' => $slug,
                'payment_channel_name' => $paymentChannelModel->title,
                'topup_user_number' => 0,
                'topup_rmb' => 0,
                'topup_rate' => 0,
                'average_topup_rmb' => 0,
            ];
            if (isset($userPerChannel[$slug])) {
                $result['topup_user_number'] = count($userPerChannel[$slug]);
            }
            if (isset($rmbPerChannel[$slug])) {
                $result['topup_rmb'] = $rmbPerChannel[$slug];
            }
            if ($loginUserNumber != 0) {
                $result['topup_rate'] = round($result['topup_user_number'] / (float)$loginUserNumber * 100) / 100;
                if ($result['topup_rate'] > 1) {
                    $result['topup_rate'] = 1;
                }
            }
            if ($result['topup_user_number'] != 0) {
                $result['average_topup_rmb'] = round($result['topup_rmb'] / (float)$result['topup_user_number'] * 100) / 100;
            }
            // 資料寫入db
            $record =  $this->analyticDailyChannelTopupRepository->findWhere([
                    'date' => $date,
                    'payment_channel_slug' => $slug
            ])->first();
            if ($record) {
                $record->update($result);
            } else {
                $this->analyticDailyChannelTopupRepository->create($result);
            }        

        }
    }

    /**
     * 每日後台充值統計資訊
     */
    public function getDailyAdminTopupApplyReport($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        $allAdminTopupModelArray = $this->goldTopupApplicationRepository->findWhere(
            [
                'status' => GoldTopupApplication::STATUS_PASS,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );
        $userNumber = [];
        $totalGold = 0;
        $length = count($allAdminTopupModelArray);

        for ($i=0; $i<$length; $i++) {
            $adminTopupModel = $allAdminTopupModelArray[$i];
            $userNumber[$adminTopupModel->user_id] = 1;
            $totalGold += $adminTopupModel->gold;
        }
        $userNumber = count($userNumber);
        $average = 0;
        if ($userNumber != 0) {
            $average = round($totalGold / (float)$userNumber * 100.0) / 100.0;
        }

        $result = [
            'date' => $date,
            'topup_user_number' => $userNumber,
            'topup_gold' => $totalGold,
            'average_topup_gold' =>$average
        ];

        // 資料寫入db
        $record =  $this->analyticDailyAdminTopupApplyReportRepository->findWhere([
            'date' => $date,
        ])->first();
        if ($record) {
            $record->update($result);
        } else {
            $this->analyticDailyAdminTopupApplyReportRepository->create($result);
        }
    }

    /**
     * 每日用戶提現統計資訊
     */
    public function getDailyUserWithdrawReport($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        $allWithdrawModelArray = $this->withDrawGoldApplyRepository->findWhere(
            [
                'status' => WithDrawGoldApply::STATUS_PASS,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );

        $userPerChannel = [];
        $rmbPerChannel = [];
        $length = count($allWithdrawModelArray);
        for ($i=0; $i<$length; $i++) {
            $withdrawModel = $allWithdrawModelArray[$i];
            $slug = $withdrawModel->payment_channels_slug;

            $userPerChannel[$slug][$withdrawModel->user_id] = 1;
            if (isset($rmbPerChannel[$slug])) {
                $rmbPerChannel[$slug] += $withdrawModel->profit;
            } else{
                $rmbPerChannel[$slug] = $withdrawModel->profit;
            }
        }

        $result = [];
        $allPaymentChannelModelArray = $this->paymentChannelRepository->all()->keyby('slug');
        foreach ($allPaymentChannelModelArray as $slug => $paymentChannelModel) {
            $result = [
                'date' => $date,
                'payment_channel_slug' => $slug,
                'payment_channel_name' => $paymentChannelModel->title,
                'withdraw_user_number' => 0,
                'withdraw_rmb' => 0,
                'average_withdraw_rmb' => 0,
            ];
            if (isset($userPerChannel[$slug])) {
                $result['withdraw_user_number'] = count($userPerChannel[$slug]);
            }
            if (isset($rmbPerChannel[$slug])) {
                $result['withdraw_rmb'] = $rmbPerChannel[$slug];
            }
            if ($result['withdraw_user_number'] != 0) {
                $result['average_withdraw_rmb'] = round($result['withdraw_rmb'] / (float)$result['withdraw_user_number'] * 100) / 100;
            }
            // 資料寫入db
            $record =  $this->analyticDailyUserWithdrawReportRepository->findWhere([
                    'date' => $date,
                    'payment_channel_slug' => $slug
            ])->first();
            if ($record) {
                $record->update($result);
            } else {
                $this->analyticDailyUserWithdrawReportRepository->create($result);
            }
        }
    }

    /**
     * 每日經紀公司提現統計資訊
     */
    public function getDailyCompanyWithdrawReport($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';

        $allWithdrawModelArray = $this->companyWithdrawRepository->findWhere(
            [
                'status' => CompanyWithdraw::STATUS_FINISH,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );

        $rmbPerCompany = [];
        $companyIdList = [];

        $length = count($allWithdrawModelArray);
        for ($i=0; $i<$length; $i++) {
            $withdrawModel = $allWithdrawModelArray[$i];
            $companyId = $withdrawModel->company_id;
            if (isset($rmbPerCompany[$companyId])) {
                $rmbPerCompany[$companyId] += $withdrawModel->real_withdraw_rmb;
            } else {
                $rmbPerCompany[$companyId] = $withdrawModel->real_withdraw_rmb;
            }

            $companyIdList[] = $companyId;
        }
        $companyIdList = array_unique($companyIdList);
        $where = [
            ['id', 'in', $companyIdList],
        ];

        $allCompanyModelArray = $this->managerRepository->findWhere(
            $where
        )->keyby('id')->all();
        foreach($rmbPerCompany as $companyId => $rmb) {
            $data = [
                'date' => $date,
                'company_id' => $companyId,
                'company_name' => $allCompanyModelArray[$companyId]->name,
                'withdraw_rmb' => $rmb,
            ];

            // 資料寫入db
            $record =  $this->analyticDailyCompanyWithdrawReportRepository->findWhere([
                'date' => $date,
                'company_id' => $companyId
            ])->first();
            if ($record) {
                $record->update($data);
            } else {
                $this->analyticDailyCompanyWithdrawReportRepository->create($data);
            }        
        }
    }

    /**
     * 每日金幣統計資訊
     */
    public function getDailyGoldStatistics($date)
    {
        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';                
        
        $allWithdrawModelArray = $this->companyWithdrawRepository->findWhere(
            [
                'status' => CompanyWithdraw::STATUS_FINISH,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );
        
        $result = [
            'date' => $date,
            'topup_gold' => 0,
            'purchase_gift_gold'=> 0,
            'purchase_prop_gold'=> 0,
            'purchase_barrage_gold'=> 0,
            'game_bet_gold'=> 0,
            'game_bet_win_gold'=> 0,
            'remain_gold'=> 0,
        ];

        $where = [
            ['created_at', '>=', $startDatetime],
            ['created_at', '<=', $endDatetime]
        ];

        // 所有用戶充值數
        $orderGoldCount = $this->userTopupOrderRepository->model()::where($where)->sum('gold');

        // 所有經紀人充值數
        $agentGoldCount = $this->agentTransactionListRepository->model()::where($where)->sum('transaction_gold');

        // 所有後台充值數
        $allAdminTopupModelArray = $this->goldTopupApplicationRepository->model()::where(
            [
                'status' => GoldTopupApplication::STATUS_PASS,
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        )->sum('gold');
        $result['topup_gold'] = $orderGoldCount + $agentGoldCount + $allAdminTopupModelArray;

        // 禮物購買金幣數
        $giftGoldCount = $this->giftTransactionOrderRepository->model()::where([
            ['created_at', '>=', $startDatetime],
            ['created_at', '<=', $endDatetime],
        ])->sum('gold_price');
        $result['purchase_gift_gold'] = $giftGoldCount;

        // 購買道具金幣數
        $allPropGift = $this->baseGiftTypeRepository->findWhere(['is_prop' => 1])->keyby('id')->all();
        $allPropSlug = [];
        foreach ($allPropGift as $giftId => $prop) {
            $allPropSlug[] = $prop->type_slug;
        }
        $allPropOrderModelArray = $this->giftTransactionOrderRepository->findWhere(
            [
                ['gift_type_id', 'in', $allPropSlug],
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime]
            ]
        );

        $propGold = 0;
        $length = count($allPropOrderModelArray);
        for ($i = 0; $i < $length; $i++) {
            $propGold += $allPropOrderModelArray[$i]->gold_price;
        }
        $result['purchase_prop_gold'] = $propGold;

        // 取得所有彈幕資訊
        $allBarrageModelArray = $this->baseBarrageTypeRepository->all()->keyby('id');
        $allBarrageOrderModelArray = $this->barrageTransactionOrderRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime]
            ]
        );
        $barrageGold = 0;
        $length = count($allBarrageOrderModelArray);
        for ($i = 0; $i < $length; $i++) {
            $barrageId = $allBarrageOrderModelArray[$i]->barrage_type_id;
            $barrageGold += $allBarrageModelArray[$barrageId]->gold_price;
        }
        $result['purchase_barrage_gold'] = $barrageGold;

        // 遊戲下注資訊
        $allGameBetRecordModelArray = $this->gameBetRecordRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime]
            ]
        )->all();
        $betGold = 0;
        $winGold = 0;
        $length = count($allGameBetRecordModelArray);
        for ($i = 0; $i < $length; $i++) {
            $betGold += $allGameBetRecordModelArray[$i]->bet_gold;
            $winGold += $allGameBetRecordModelArray[$i]->win_gold;
        }
        $result['game_bet_gold'] = $betGold;
        $result['game_bet_win_gold'] = $winGold;
        $result['reman_gold'] = $result['topup_gold'] - $result['purchase_gift_gold'] - $result['purchase_prop_gold'] - $result['purchase_barrage_gold'] - $result['game_bet_gold'] + $result['game_bet_win_gold'];
        // 資料寫入db
        $record =  $this->analyticGoldStatisticsReportRepository->findWhere([
            'date' => $date,
        ])->first();
        if ($record) {
            $record->update($result);
        } else {
            $this->analyticGoldStatisticsReportRepository->create($result);
        }
    
    }
}
