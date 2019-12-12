<?php

namespace App\Services;

use App\Models\Manager as ManagerModel;
use App\Repositories\Interfaces\AnalyticManagerCompanyGoldDetailReportRepository;
use App\Repositories\Interfaces\AnalyticManagerCompanyGoldReportRepository;
use App\Repositories\Interfaces\AnchorInfoRepository;
use App\Repositories\Interfaces\BaseGiftTypeRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;
use App\Repositories\Interfaces\ManagerRepository;
use App\Repositories\Interfaces\UserRepository;

//经纪公司报表服务
class ManagerCompanyReportService
{
    private $userRepository;
    private $baseGiftTypeRepository;
    private $managerRepository;
    private $anchorInfoRepository;
    private $giftTransactionOrderRepository;
    private $analyticManagerCompanyGoldReportRepository;
    private $analyticManagerCompanyGoldDetailReportRepository;

    const FIRST_ISSUE = 'first';
    const SECOND_ISSUE = 'second';
    const THIRD_ISSUE = 'third';

    public function __construct(
        UserRepository $userRepository,
        BaseGiftTypeRepository $baseGiftTypeRepository,
        ManagerRepository $managerRepository,
        AnchorInfoRepository $anchorInfoRepository,
        GiftTransactionOrderRepository $giftTransactionOrderRepository,
        AnalyticManagerCompanyGoldReportRepository $analyticManagerCompanyGoldReportRepository,
        AnalyticManagerCompanyGoldDetailReportRepository $analyticManagerCompanyGoldDetailReportRepository
    ) {
        $this->userRepository = $userRepository;

        $this->baseGiftTypeRepository = $baseGiftTypeRepository;

        $this->managerRepository = $managerRepository;

        $this->anchorInfoRepository = $anchorInfoRepository;

        $this->giftTransactionOrderRepository = $giftTransactionOrderRepository;

        $this->analyticManagerCompanyGoldReportRepository = $analyticManagerCompanyGoldReportRepository;

        $this->analyticManagerCompanyGoldDetailReportRepository = $analyticManagerCompanyGoldDetailReportRepository;
    }

    /**
     * 產出所有的報表
     *
     * @param string $startDate
     * @param string $endDate
     */
    public function makeAllReport($startDate = '', $endDate = '')
    {
        // 取得時間範圍
        $datetimeRange = $this->getDatetimeRange($startDate, $endDate);
        $startDatetime = $datetimeRange['start'];
        $endDatetime = $datetimeRange['end'];

        print_r('日期範圍: ' . $startDatetime . ' ~ ' . $endDatetime . PHP_EOL);

        print_r('開始製作總報表...');
        $this->makeCompanyReport($startDatetime, $endDatetime);
        print_r(' 完成' . PHP_EOL);

        print_r('開始製作詳細報表...');
        $this->makeCompanyDetailReport($startDatetime, $endDatetime);
        print_r('完成' . PHP_EOL);

        print_r('開始結算任務禮物');
        $this->startSettleMissionGift($startDatetime, $endDatetime);
        print_r(' 完成' . PHP_EOL);
    }

    /**
     * 依照 '期' 來製作報表
     */
    public function makeAllReportByIssue($issue)
    {
        $startData = '';
        $endDate = '';
        // 取得本月的日期範圍
        $range = getManagerCompanyReportSettledRange();
        switch ($issue) {
            case self::FIRST_ISSUE:
                // 取得本月的第一期日期範圍
                $startData = $range['first_range']['start'];
                $endDate = $range['first_range']['end'];
                break;
            case self::SECOND_ISSUE:
                // 取得本月的第二期日期範圍
                $startData = $range['second_range']['start'];
                $endDate = $range['second_range']['end'];
                break;
            case self::THIRD_ISSUE:
                // 取得上個月的第三期日期範圍
                $range = getManagerCompanyReportSettledRange(-1);
                $startData = $range['third_range']['start'];
                $endDate = $range['third_range']['end'];
                break;
            default:
                print_r('錯誤的issue, 參數issue請輸入first, second 或 third' . PHP_EOL);
                return;
        }
        $this->makeAllReport($startData, $endDate);
    }

    protected function startSettleMissionGift($startDatetime = '', $endDatetime = '')
    {
        // 取得所有id和model資料
        $allIdAndModel = $this->getAllIdAndModelForCompanyAndAnchor();
        // 所有經紀公司id
        $allCompanyIdList = $allIdAndModel['company']['id_list'];

        // 所有主播id
        $allAnchorIdList = $allIdAndModel['anchor']['id_list'];
        // 主播id => 經紀公司id
        $allAnchorToCompanyList = $allIdAndModel['anchor_to_company'];

        // 取得所有禮物的交易的資料
        $allReceiveModel = $this->giftTransactionOrderRepository->scopeQuery(
            function ($query) use ($startDatetime, $endDatetime, $allAnchorIdList) {
                return $query->where([
                    ['created_at', '>=', $startDatetime],
                    ['created_at', '<=', $endDatetime]])
                    ->whereIn('receive_uid', $allAnchorIdList);
            }
        )->get()->all();

        // 所有主播的用戶資料,  $allAnchorBasicInfo[主播id] => 資料
        $allAnchorBasicInfo = $this->getAnchorBasicDataListByModel($allAnchorIdList);

        // 任務禮物的slug
        $allMissionGiftSlugList = $allIdAndModel['gift']['mission_slug_list'];

        // 禮物的價格
        $allGiftPrice = $allIdAndModel['gift']['all_price'];

        // 每一位主播的任務禮物的交易資訊
        $allReceiveMissionGiftData = $this->makeAnchorMissionGiftData($allReceiveModel, $allIdAndModel['gift']);

        // 每一位主播的任務禮物總收入, anchor id => total gold reward
        $allAnchorIdToAddGold = [];

        // 每一經紀公司的任務禮物總收入, cmopany id => total gold reward
        $allCompanyIdToAddGold = [];

        // 從datetime取得date
        $startDate = explode(" ", $startDatetime)[0];
        $endDate = explode(" ", $endDatetime)[0];

        // 每一個經紀公司當期的總報表資料
        $allCompanyReportModel = $this->analyticManagerCompanyGoldReportRepository->scopeQuery(
            function ($query) use ($startDate, $endDate, $allCompanyIdList) {
                return $query->where([
                    ['start_date', '=', $startDate],
                    ['end_date', '=', $endDate]])
                    ->whereIn('company_id', $allCompanyIdList
                    );
            }
        )->get()->all();

        $companyIdToReportModel = [];
        for ($i = 0; $i < count($allCompanyReportModel); $i++) {
            $companyIdToReportModel[$allCompanyReportModel[$i]->company_id] = $allCompanyReportModel[$i];
        }

        // 每一個主播當期的詳細報表資料
        $allAnchorReportModel = $this->analyticManagerCompanyGoldDetailReportRepository->scopeQuery(
            function ($query) use ($startDate, $endDate, $allAnchorIdList) {
                return $query->where([
                    ['start_date', '=', $startDate],
                    ['end_date', '=', $endDate]])
                    ->whereIn('user_id', $allAnchorIdList
                    );
            }
        )->get()->all();

        $anchorIdToReportModel = [];
        for ($i = 0; $i < count($allAnchorReportModel); $i++) {
            $anchorIdToReportModel[$allAnchorReportModel[$i]->user_id] = $allAnchorReportModel[$i];
        }

        for ($i = 0; $i < count($allReceiveModel); $i++) {
            // 禮物交易資料
            $receiveModel = $allReceiveModel[$i];
            $anchorId = $receiveModel->receive_uid;
            $companyId = $allAnchorToCompanyList[$anchorId];
            $giftSlug = $receiveModel->gift_type_id;

            $anchorReceiveGold = 0;
            $companyReceiveGold = 0;

            // 任務禮物的收益計算
            if (in_array($giftSlug, $allMissionGiftSlugList)) {
                $propotionData = $allReceiveMissionGiftData[$allReceiveModel[$i]->receive_uid][$allReceiveModel[$i]->gift_type_id];
                $giftPrice = $allGiftPrice[$allReceiveModel[$i]->gift_type_id];

                // 依照佔成計算真實可獲得的金幣數量
                $realObtainGold = $this->getRealObtainGoldByPropotion($giftPrice, $propotionData);
                $anchorReceiveGold = $realObtainGold['anchor'];
                $companyReceiveGold = $realObtainGold['company'];

                // 將主播增加的金幣收入統計到 allAnchorIdToAddGold
                if (isset($allAnchorIdToAddGold[$anchorId])) {
                    $allAnchorIdToAddGold[$anchorId] += $anchorReceiveGold;
                } else {
                    $allAnchorIdToAddGold[$anchorId] = $anchorReceiveGold;
                }

                // 將經紀公司增加的金幣收入統計到 allCompanyIdToAddGold
                if (isset($allCompanyIdToAddGold[$companyId])) {
                    $allCompanyIdToAddGold[$companyId] += $companyReceiveGold;
                } else {
                    $allCompanyIdToAddGold[$companyId] = $companyReceiveGold;
                }
            }
        }

        // 所有主播的user model
        $allAnchorUserModel = $this->getAllUserModelByIds($allAnchorIdList);
        // 所有公司的model
        $allCompanyModel = $allIdAndModel['company']['model'];

        // 更新所有主播的金幣, 現有金幣 + 任務禮物總收入
        for ($i = 0; $i < count($allAnchorUserModel); $i++) {
            $anchorUserModel = $allAnchorUserModel[$i];
            $anchorUserId = $anchorUserModel->id;
            $obtainGold = 0;
            if (isset($allAnchorIdToAddGold[$anchorUserId])) {
                $obtainGold += $allAnchorIdToAddGold[$anchorUserId];
            }
            if ($obtainGold == 0) {
                continue;
            }
            $newGold = $anchorUserModel->gold + $obtainGold;
            //$anchorUserModel->updateGold($newGold);

            // 更新用戶金幣數量
            $anchorReportModel = $anchorIdToReportModel[$anchorUserId];
            $this->userRepository->addGold($anchorUserModel, $obtainGold, $anchorReportModel);
        }
        // 更新所有經紀公司的金幣, 現有金幣 + 任務禮物總收入
        for ($i = 0; $i < count($allCompanyModel); $i++) {
            $companyModel = $allCompanyModel[$i];
            $companyId = $companyModel->id;
            $obtainGold = 0;
            if (isset($allCompanyIdToAddGold[$companyId])) {
                $obtainGold += $allCompanyIdToAddGold[$companyId];
            }
            if ($obtainGold == 0) {
                continue;
            }
            $newGold = $companyModel->gold + $obtainGold;
            //$companyModel->updateGold($newGold);

            // 更新金幣數量
            $companyReportModel = $companyIdToReportModel[$companyId];
            $this->managerRepository->updateGold($companyModel, $newGold, $companyReportModel);
        }
    }

    /**
     * 產出經紀公司詳細報表
     *
     * @param string $startDatetime
     * @param string $endDatetime
     */
    protected function makeCompanyDetailReport($startDatetime = '', $endDatetime = '')
    {
        // 取得所有報表相關的資料
        $allIdAndModel = $this->getAllIdAndModelForCompanyAndAnchor();
        // 所有主播id
        $allAnchorIdList = $allIdAndModel['anchor']['id_list'];
        // 主播id => 經紀公司id
        $allAnchorToCompanyList = $allIdAndModel['anchor_to_company'];

        // 取得所有禮物的交易的資料
        $allReceiveModel = $this->giftTransactionOrderRepository->scopeQuery(
            function ($query) use ($startDatetime, $endDatetime, $allAnchorIdList) {
                return $query->where([
                    ['created_at', '>=', $startDatetime],
                    ['created_at', '<=', $endDatetime]])
                    ->whereIn('receive_uid', $allAnchorIdList);
            }
        )->get()->all();

        // 準備要寫入db的資料
        $allReportData = $this->makeCompanyDetailReportData($startDatetime, $endDatetime, $allAnchorIdList, $allReceiveModel, $allAnchorToCompanyList, $allIdAndModel['gift']);

        if (count($allReportData) > 0) {
            $this->analyticManagerCompanyGoldDetailReportRepository->makeModel()->multiInsertOrUpdate($allReportData);
        }
    }

    /**
     * 產出經紀公司報表
     *
     * @param string $startDatetime
     * @param string $endDatetime
     */
    protected function makeCompanyReport($startDatetime = '', $endDatetime = '')
    {
        // 取得所有報表相關的資料
        $allIdAndModel = $this->getAllIdAndModelForCompanyAndAnchor();

        // 取得所有非任務的禮物id
        $nonMissionGiftSlugList = array_diff($allIdAndModel['gift']['all_slug_list'], $allIdAndModel['gift']['mission_slug_list']);
        // 所有經紀公司id
        $allCompanyIdList = $allIdAndModel['company']['id_list'];
        // 所有經紀人id
        $allManagerInCompanyIdList = $allIdAndModel['manager']['id_list'];
        // 所有主播id
        $allAnchorIdList = $allIdAndModel['anchor']['id_list'];
        // 主播id => 經紀公司id
        $allAnchorToCompanyList = $allIdAndModel['anchor_to_company'];

        // 取得所有禮物的交易的資料
        $allReceiveModel = $this->giftTransactionOrderRepository->scopeQuery(
            function ($query) use ($startDatetime, $endDatetime, $allAnchorIdList) {
                return $query->where([
                    ['created_at', '>=', $startDatetime],
                    ['created_at', '<=', $endDatetime]])
                    ->whereIn('receive_uid', $allAnchorIdList);
            }
        )->get()->all();

        // 準備要寫入db的資料
        $allReportData = $this->makeCompanyReportData($startDatetime, $endDatetime, $allReceiveModel, $allAnchorToCompanyList, $allManagerInCompanyIdList, $allIdAndModel['gift']);
        // 使用 multiInsertOrUpdate 寫入db
        if (count($allReportData) > 0) {
            $this->analyticManagerCompanyGoldReportRepository->makeModel()->multiInsertOrUpdate($allReportData);
        }
    }

    /**
     * 製作要寫入經紀公司詳細報表的資料
     */
    private function makeCompanyDetailReportData($startDatetime, $endDatetime, $allAnchorIdList, $allReceiveModel, $allAnchorToCompanyList, $allGiftIdAndModel)
    {
        // 所有主播的用戶資料,  $allAnchorBasicInfo[主播id] => 資料
        $allAnchorBasicInfo = $this->getAnchorBasicDataListByModel($allAnchorIdList);

        // 各類禮物的Slug
        $allGiftSlugList = $allGiftIdAndModel['all_slug_list'];
        $allNormalGiftSlugList = $allGiftIdAndModel['normal_slug_list'];
        $allBigGiftSlugList = $allGiftIdAndModel['big_slug_list'];
        $allPropGiftSlugList = $allGiftIdAndModel['prop_slug_list'];
        $allMissionGiftSlugList = $allGiftIdAndModel['mission_slug_list'];

        // 禮物的價格
        $allGiftPrice = $allGiftIdAndModel['all_price'];

        // 每一位主播的任務禮物的交易資訊
        $allReceiveMissionGiftData = $this->makeAnchorMissionGiftData($allReceiveModel, $allGiftIdAndModel);

        $allReportData = [];
        for ($i = 0; $i < count($allReceiveModel); $i++) {
            // 禮物交易資料
            $receiveModel = $allReceiveModel[$i];
            $anchorId = $receiveModel->receive_uid;
            $companyId = $allAnchorToCompanyList[$anchorId];
            $giftSlug = $receiveModel->gift_type_id;

            $anchorReceiveGold = 0;
            $companyReceiveGold = 0;

            // 任務禮物的收益計算
            if (in_array($giftSlug, $allMissionGiftSlugList)) {
                $propotionData = $allReceiveMissionGiftData[$allReceiveModel[$i]->receive_uid][$allReceiveModel[$i]->gift_type_id];
                $giftPrice = $allGiftPrice[$allReceiveModel[$i]->gift_type_id];

                // 依照佔成計算真實可獲得的金幣數量
                $realObtainGold = $this->getRealObtainGoldByPropotion($giftPrice, $propotionData);

                $anchorReceiveGold = $realObtainGold['anchor'];
                $companyReceiveGold = $realObtainGold['company'];

            } else {
                $anchorReceiveGold = $receiveModel->anchor_real_receive_gold;
                $companyReceiveGold = $receiveModel->company_real_receive_gold;
            }

            // 大禮物金幣
            if (in_array($giftSlug, $allBigGiftSlugList)) {
                $bigIncome = $anchorReceiveGold;
                $bigNum = 1;
            } else {
                $bigIncome = 0;
                $bigNum = 0;
            }

            // 任務禮物金幣
            if (in_array($giftSlug, $allMissionGiftSlugList)) {
                $missionIncome = $anchorReceiveGold;
                $missionNum = 1;
            } else {
                $missionIncome = 0;
                $missionNum = 0;
            }

            // 一般禮物金幣
            if (in_array($giftSlug, $allNormalGiftSlugList)) {
                $normalIncome = $anchorReceiveGold;
                $normalNum = 1;
            } else {
                $normalIncome = 0;
                $normalNum = 0;
            }

            if (isset($allReportData[$anchorId]) == false) {
                $allReportData[$anchorId]['company_id'] = $companyId;

                $allReportData[$anchorId]['start_date'] = $startDatetime;
                $allReportData[$anchorId]['end_date'] = $endDatetime;

                $allReportData[$anchorId]['user_id'] = $anchorId;
                $allReportData[$anchorId]['nickname'] = $allAnchorBasicInfo[$anchorId]['nickname'];

                $allReportData[$anchorId]['total_income'] = $anchorReceiveGold + $companyReceiveGold;
                $allReportData[$anchorId]['total_company_income'] = $companyReceiveGold;
                $allReportData[$anchorId]['total_anchor_income'] = $anchorReceiveGold;

                $allReportData[$anchorId]['receive_normal_gift'] = $normalNum;
                $allReportData[$anchorId]['normal_gift_income'] = $normalIncome;

                $allReportData[$anchorId]['receive_big_gift'] = $bigNum;
                $allReportData[$anchorId]['big_gift_income'] = $bigIncome;

                $allReportData[$anchorId]['receive_mission_gift'] = $missionNum;
                $allReportData[$anchorId]['mission_gift_income'] = $missionIncome;
            } else {
                $allReportData[$anchorId]['total_income'] += $anchorReceiveGold + $companyReceiveGold;
                $allReportData[$anchorId]['total_company_income'] += $companyReceiveGold;
                $allReportData[$anchorId]['total_anchor_income'] += $anchorReceiveGold;

                $allReportData[$anchorId]['receive_normal_gift'] += $normalNum;
                $allReportData[$anchorId]['normal_gift_income'] += $normalIncome;

                $allReportData[$anchorId]['receive_big_gift'] += $bigNum;
                $allReportData[$anchorId]['big_gift_income'] += $bigIncome;

                $allReportData[$anchorId]['receive_mission_gift'] += $missionNum;
                $allReportData[$anchorId]['mission_gift_income'] += $missionIncome;
            }
        }
        return $allReportData;
    }
    /**
     * 製作要寫入經紀公司報表的資料
     *
     * @param string $startDatetime
     * @param string $endDatetime
     * @param array $allReceiveModel 取得所有任務禮物的交易的資料
     * @param array $allAnchorToCompanyList 主播id => 經紀公司id
     * @param array $allManagerInCompanyIdList 所有經紀人id
     */
    private function makeCompanyReportData($startDatetime, $endDatetime, $allReceiveModel, $allAnchorToCompanyList, $allManagerInCompanyIdList, $allGiftIdAndModel)
    {
        // 每一位主播獲得的金幣
        $allAnchorReceiveGold = [];

        // 每一個經紀公司獲得的金幣
        $allCompanyReceiveGold = [];

        // 依照經紀公司來區分每一位主播獲得的金幣
        $allAnchorInCompanyReceiveGold = [];

        // 各類禮物的id
        $allGiftSlugList = $allGiftIdAndModel['all_slug_list'];
        $allNormalGiftSlugList = $allGiftIdAndModel['normal_slug_list'];
        $allBigGiftSlugList = $allGiftIdAndModel['big_slug_list'];
        $allPropGiftSlugList = $allGiftIdAndModel['prop_slug_list'];
        $allMissionGiftSlugList = $allGiftIdAndModel['mission_slug_list'];

        // 禮物的價格
        $allGiftPrice = $allGiftIdAndModel['all_price'];

        // 每一位主播的任務禮物的交易資訊
        $allReceiveMissionGiftData = $this->makeAnchorMissionGiftData($allReceiveModel, $allGiftIdAndModel);

        for ($i = 0; $i < count($allReceiveModel); $i++) {
            $anchorRealGold = 0;
            $companyRealGold = 0;

            // 任務禮物處理
            if (in_array($allReceiveModel[$i]->gift_type_id, $allMissionGiftSlugList)) {
                $propotionData = $allReceiveMissionGiftData[$allReceiveModel[$i]->receive_uid][$allReceiveModel[$i]->gift_type_id];
                $giftPrice = $allGiftPrice[$allReceiveModel[$i]->gift_type_id];

                // 依照佔成計算真實可獲得的金幣數量
                $realObtainGold = $this->getRealObtainGoldByPropotion($giftPrice, $propotionData);

                $anchorRealGold = $realObtainGold['anchor'];
                $companyRealGold = $realObtainGold['company'];
            } else {
                $anchorRealGold = $allReceiveModel[$i]->anchor_real_receive_gold;
                $companyRealGold = $allReceiveModel[$i]->company_real_receive_gold;
            }

            // 組成 allAnchorReceiveGold
            $anchorId = $allReceiveModel[$i]->receive_uid;
            if (isset($allAnchorReceiveGold[$anchorId])) {
                $allAnchorReceiveGold[$anchorId] += $anchorRealGold;
            } else {
                $allAnchorReceiveGold[$anchorId] = $anchorRealGold;
            }

            // 組成 allCompanyReceiveGold
            $companyId = $allAnchorToCompanyList[$anchorId];
            if (isset($allCompanyReceiveGold[$companyId])) {
                $allCompanyReceiveGold[$companyId] += $companyRealGold;
            } else {
                $allCompanyReceiveGold[$companyId] = $companyRealGold;
            }

            // 組成 allAnchorInCompanyReceiveGold
            if (!isset($allAnchorInCompanyReceiveGold[$companyId])) {
                $allAnchorInCompanyReceiveGold[$companyId] = [];
                $allAnchorInCompanyReceiveGold[$companyId]['total'] = 0;
            }
            if (isset($allAnchorInCompanyReceiveGold[$companyId][$anchorId])) {
                $allAnchorInCompanyReceiveGold[$companyId][$anchorId] += $anchorRealGold;
            } else {
                $allAnchorInCompanyReceiveGold[$companyId][$anchorId] = $anchorRealGold;
            }
            $allAnchorInCompanyReceiveGold[$companyId]['total'] += $anchorRealGold;
        }

        // 準備要寫入db的資料
        $allReportData = [];
        foreach ($allCompanyReceiveGold as $companyId => $receiveGold) {
            $reportData = [
                'company_id' => $companyId,
                'start_date' => $startDatetime,
                'end_date' => $endDatetime,
                'manager_number' => count($allManagerInCompanyIdList[$companyId]),
                'anchor_number' => count($allAnchorInCompanyReceiveGold[$companyId]) - 1,
                'total_company_gold_income' => $receiveGold,
                'total_anchor_gold_income' => $allAnchorInCompanyReceiveGold[$companyId]['total'],
            ];
            $allReportData[] = $reportData;
        }

        return $allReportData;
    }

    /**
     * 取得時間範圍
     */
    private function getDatetimeRange($startDate = '', $endDate = '')
    {
        // 取得日期範圍
        if ($startDate == '' || $endDate == '') {
            $result = getManagerCompanyReportSettledRange();
            $startDate = $result['first_range']['start'];
            $endDate = $result['first_range']['end'];
        }
        // 將日期加上時分秒
        $startDatetime = dateToDatetime($startDate, true);
        $endDatetime = dateToDatetime($endDate, false);

        return ['start' => $startDatetime, 'end' => $endDatetime];
    }

    /**
     * 取得所有company, anchor相關的id與model資料
     */
    private function getAllIdAndModelForCompanyAndAnchor()
    {
        $giftTypeData = $this->getAllGiftTypeSlugAndModel();

        $companyData = $this->getAllCompanyIdAndModel();
        $allCompanyIdList = $companyData['id_list'];

        $managerData = $this->getAllManagerIdInCompanyAndModel($allCompanyIdList);
        $anchorData = $this->getAllAnchorIdInCompanyAndModel($allCompanyIdList);
        $allAnchorModel = $anchorData['model'];
        $allAnchorIdList = $anchorData['id_list'];

        // 經濟公司id => [主播id1, 主播id2 ...]
        $allCompanyToAnchorList = [];
        // 主播id => 經濟公司id
        $allAnchorToCompanyList = [];

        for ($i = 0; $i < count($allAnchorModel); $i++) {
            $allCompanyToAnchorList[$allAnchorModel[$i]->company_id][] = $allAnchorModel[$i]->user_id;
            $allAnchorToCompanyList[$allAnchorModel[$i]->user_id] = $allAnchorModel[$i]->company_id;
        }

        return [
            'gift' => $giftTypeData,
            'company' => $companyData,
            'manager' => $managerData,
            'anchor' => $anchorData,
            'anchor_to_company' => $allAnchorToCompanyList,
            'company_to_anchor' => $allCompanyToAnchorList,
        ];
    }

    /**
     * 取得有禮物slug和model
     */
    private function getAllGiftTypeSlugAndModel()
    {
        $allGiftCollection = $this->baseGiftTypeRepository->all();
        $allGiftModel = $allGiftCollection->all();

        $allSlugList = [];
        $normalSlugList = [];
        $missionSlugList = [];
        $bigSlugList = [];
        $propSlugList = [];
        $missionPropotionList = [];
        $allPrice = [];

        for ($i = 0; $i < count($allGiftModel); $i++) {
            $allSlugList[] = $allGiftModel[$i]->type_slug;
            $allPrice[$allGiftModel[$i]->type_slug] = $allGiftModel[$i]->gold_price;

            if ($allGiftModel[$i]->is_mission) {
                $missionSlugList[] = $allGiftModel[$i]->type_slug;
                $missionPropotionList[$allGiftModel[$i]->type_slug] = $allGiftModel[$i]->propotion_list;
            }
            if ($allGiftModel[$i]->is_prop) {
                $propSlugList[] = $allGiftModel[$i]->type_slug;
            }
            if ($allGiftModel[$i]->is_big) {
                $bigSlugList[] = $allGiftModel[$i]->type_slug;
            }
            if ((!$allGiftModel[$i]->is_mission) && (!$allGiftModel[$i]->is_prop) && (!$allGiftModel[$i]->is_big)) {
                $normalSlugList[] = $allGiftModel[$i]->type_slug;
            }
        }

        return [
            'all_model' => $allGiftModel,
            'all_price' => $allPrice,
            'all_slug_list' => $allSlugList,
            'normal_slug_list' => $normalSlugList,
            'mission_slug_list' => $missionSlugList,
            'prop_slug_list' => $propSlugList,
            'big_slug_list' => $bigSlugList,
            'mission_propotion_list' => $missionPropotionList,
        ];
    }

    /**
     * 取得所有經濟公司id和model
     */
    private function getAllCompanyIdAndModel()
    {
        // 取得所有經濟公司id
        $allCompanyCollection = $this->managerRepository->findWhere(['parent_id' => ManagerModel::COMPANY_PARENT_ID]);
        $allCompanyModel = $allCompanyCollection->all();
        $allCompanyIdList = [];
        for ($i = 0; $i < count($allCompanyModel); $i++) {
            $allCompanyIdList[] = $allCompanyModel[$i]->id;
        }
        return ['id_list' => $allCompanyIdList, 'model' => $allCompanyModel];
    }

    /**
     * 取得所有經紀人id和model
     */
    private function getAllManagerIdInCompanyAndModel($allCompanyIdList)
    {
        // 取得所有經濟人id
        $allManagerCollection = $this->managerRepository->findWhereIn('parent_id', $allCompanyIdList);
        $allManagerModel = $allManagerCollection->all();
        $allManagerInCompanyIdList = [];
        for ($i = 0; $i < count($allManagerModel); $i++) {
            $allManagerInCompanyIdList[$allManagerModel[$i]->parent_id][] = $allManagerModel[$i]->id;
        }
        return ['id_list' => $allManagerInCompanyIdList, 'model' => $allManagerModel];
    }

    /**
     * 取得所有主播id和model
     */
    private function getAllAnchorIdInCompanyAndModel($allCompanyIdList)
    {
        // 取得所有主播id
        $allAnchorCollection = $this->anchorInfoRepository->findWhereIn('company_id', $allCompanyIdList);
        $allAnchorModel = $allAnchorCollection->all();
        $allAnchorIdList = [];
        for ($i = 0; $i < count($allAnchorModel); $i++) {
            $allAnchorIdList[] = $allAnchorModel[$i]->user_id;
        }
        return ['id_list' => $allAnchorIdList, 'model' => $allAnchorModel];
    }

    /**
     * 取得主播的基本資訊
     */
    private function getAnchorBasicDataListByModel($allAnchorIdList)
    {
        $allUserCollection = $this->userRepository->findWhereIn('id', $allAnchorIdList);
        $allUserModel = $allUserCollection->all();
        $anchorDataList = [];

        for ($i = 0; $i < count($allUserModel); $i++) {
            $userModel = $allUserModel[$i];

            $anchorDataList[$userModel->id] = $userModel->toArray();
        }
        return $anchorDataList;
    }

    /**
     * 用很多id編號來取得很多使用者的model
     */
    private function getAllUserModelByIds($ids)
    {
        $allUserCollection = $this->userRepository->findWhereIn('id', $ids);
        $allUserModel = $allUserCollection->all();
        return $allUserModel;
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
     * 製作每一位主播的任務禮物交易資料
     */
    private function makeAnchorMissionGiftData($allReceiveModel, $allGiftIdAndModel)
    {
        // 任務禮物的id
        $missionGiftSlugList = $allGiftIdAndModel['mission_slug_list'];
        $missionPropotionList = $allGiftIdAndModel['mission_propotion_list'];
        $result = [];
        for ($i = 0; $i < count($allReceiveModel); $i++) {

            $receiveModel = $allReceiveModel[$i];
            if (in_array($receiveModel->gift_type_id, $missionGiftSlugList) == false) {
                continue;
            }
            if (isset($result[$receiveModel->receive_uid][$receiveModel->gift_type_id]) == false) {
                $result[$receiveModel->receive_uid][$receiveModel->gift_type_id] = [
                    'number' => 1,
                    'anchor_propotion' => 0,
                    'company_propotion' => 0,
                ];
            } else {
                $result[$receiveModel->receive_uid][$receiveModel->gift_type_id]['number'] += 1;
            }
        }

        foreach ($result as $userId => $allPropotionData) {
            foreach ($result[$userId] as $giftId => $propotionData) {
                $receiveTimes = $propotionData['number'];
                $data = $this->getMissionPropotionByReceiveTimes($missionPropotionList, $giftId, $receiveTimes);
                $result[$userId][$giftId]['anchor_propotion'] = $data['anchor_propotion'];
                $result[$userId][$giftId]['company_propotion'] = $data['company_propotion'];
            }
        }

        return $result;
    }

    /**
     * 依據收禮次數和禮物類型, 來取得任務禮物的佔成資料
     */
    private function getMissionPropotionByReceiveTimes($missionPropotionList, $giftTypeId, $receiveTimes)
    {
        if (!isset($missionPropotionList[$giftTypeId])) {
            return [
                'receive_times' => 0,
                'anchor_propotion' => 0,
                'company_propotion' => 0,
            ];
        }
        $propotionIndex = 0;
        $lastPropotionReceiveTimes = -1;

        $propotionList = $missionPropotionList[$giftTypeId];

        for ($i = 0; $i < count($propotionList); $i++) {

            if ($receiveTimes > $propotionList[$i]['receive_times'] &&
                $propotionList[$i]['receive_times'] > $lastPropotionReceiveTimes) {

                $lastPropotionReceiveTimes = $propotionList[$i]['receive_times'];

                $propotionIndex = $i;
            }
        }

        return [
            'receive_times' => $propotionList[$propotionIndex]['receive_times'],
            'anchor_propotion' => $propotionList[$propotionIndex]['anchor_propotion'],
            'company_propotion' => $propotionList[$propotionIndex]['company_propotion'],
        ];
    }
}
