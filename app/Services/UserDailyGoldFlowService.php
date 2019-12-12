<?php

namespace App\Services;

use App\Repositories\Interfaces\AnalyticUserDailyGoldFlowReportRepository;
use App\Repositories\Interfaces\UserGoldFlowRepository;
use \Carbon\Carbon;

//用户当天金流服务
class UserDailyGoldFlowService
{
    private $userGoldFlowRepository;

    private $analyticUserDailyGoldFlowReportRepository;

    public function __construct(
        UserGoldFlowRepository $userGoldFlowRepository,
        AnalyticUserDailyGoldFlowReportRepository $analyticUserDailyGoldFlowReportRepository) {
        $this->userGoldFlowRepository = $userGoldFlowRepository;
        $this->analyticUserDailyGoldFlowReportRepository = $analyticUserDailyGoldFlowReportRepository;
    }

    public function make($date = '')
    {
        $purchaseTypeModel = config('modelgoldtype.purchase');
        $agentTypeModel = config('modelgoldtype.agent');
        $receiveTypeModel = config('modelgoldtype.receive');
        $storeTypeModel = config('modelgoldtype.store');
        $gameTypeModel = config('modelgoldtype.game');

        // 預設產出昨日的數據
        if ($date == '') {
            $date = Carbon::now()->addDays(-1)->format('Y-m-d');
        }
        $startDatetime = $date . ' 00:00:00';
        $endDatetime = $date . ' 23:59:59';
        //dd( $startDatetime, $endDatetime);
        $allCollection = $this->userGoldFlowRepository->findWhere(
            [
                ['created_at', '>=', $startDatetime],
                ['created_at', '<=', $endDatetime],
            ]
        );
        $allGoldFlowModelArray = $allCollection->all();

        $result = [];
        for ($i = 0; $i < count($allGoldFlowModelArray); $i++) {
            $goldFlowModel = $allGoldFlowModelArray[$i];

            $sourceModelName = $goldFlowModel->source_model_name;
            $opGold = $goldFlowModel->gold_operation;
            $inGold = 0;
            $outGold = 0;

            $purchaseOutGold = 0;
            $agentOutGold = 0;
            $gameOutGold = 0;
            $otherOutGold = 0;

            $receiveInGold = 0;
            $agentInGold = 0;
            $storeInGold = 0;
            $gameInGold = 0;
            $otherInGold = 0;

            if ($opGold < 0) {
                $outGold = $opGold;
                if (in_array($sourceModelName, $purchaseTypeModel)) {
                    $purchaseOutGold += $opGold;
                }
                if (in_array($sourceModelName, $agentTypeModel)) {
                    $agentOutGold += $opGold;
                }
                if (in_array($sourceModelName, $gameTypeModel)) {
                    $gameOutGold += $opGold;
                }
                if (in_array($sourceModelName, $purchaseTypeModel) == false &&
                    in_array($sourceModelName, $agentTypeModel) == false &&
                    in_array($sourceModelName, $gameTypeModel) == false) {

                    $otherOutGold += $opGold;
                }
            }

            if ($opGold > 0) {
                $inGold = $opGold;
                if (in_array($sourceModelName, $receiveTypeModel)) {
                    $receiveInGold += $opGold;
                }
                if (in_array($sourceModelName, $agentTypeModel)) {
                    $agentInGold += $opGold;
                }
                if (in_array($sourceModelName, $storeTypeModel)) {
                    $storeInGold += $opGold;
                }
                if (in_array($sourceModelName, $gameTypeModel)) {
                    $gameInGold += $opGold;
                }
                if (in_array($sourceModelName, $purchaseTypeModel) == false &&
                    in_array($sourceModelName, $agentTypeModel) == false &&
                    in_array($sourceModelName, $storeTypeModel) == false &&
                    in_array($sourceModelName, $gameTypeModel) == false) {
                    $otherInGold += $opGold;
                }
            }

            if (isset($result[$goldFlowModel->user_id]) == false) {
                $result[$goldFlowModel->user_id] = [
                    'user_id' => $goldFlowModel->user_id,
                    'date' => $date,
                    'gold' => $opGold,

                    'in_gold' => $inGold,
                    'out_gold' => $outGold,

                    'purchase_out_gold' => $purchaseOutGold,
                    'agent_out_gold' => $agentOutGold,
                    'game_out_gold' => $gameOutGold,
                    'other_out_gold' => $otherOutGold,

                    'receive_in_gold' => $receiveInGold,
                    'store_in_gold' => $storeInGold,
                    'agent_in_gold' => $agentInGold,
                    'game_in_gold' => $gameInGold,
                    'other_In_gold' => $otherInGold,
                ];
            } else {
                $result[$goldFlowModel->user_id]['gold'] += $opGold;

                $result[$goldFlowModel->user_id]['in_gold'] += $inGold;
                $result[$goldFlowModel->user_id]['out_gold'] += $outGold;

                $result[$goldFlowModel->user_id]['purchase_out_gold'] += $purchaseOutGold;
                $result[$goldFlowModel->user_id]['agent_out_gold'] += $agentOutGold;
                $result[$goldFlowModel->user_id]['game_out_gold'] += $gameOutGold;
                $result[$goldFlowModel->user_id]['other_out_gold'] += $otherOutGold;

                $result[$goldFlowModel->user_id]['receive_in_gold'] += $receiveInGold;
                $result[$goldFlowModel->user_id]['store_in_gold'] += $storeInGold;
                $result[$goldFlowModel->user_id]['agent_in_gold'] += $agentInGold;
                $result[$goldFlowModel->user_id]['game_in_gold'] += $gameInGold;
                $result[$goldFlowModel->user_id]['other_In_gold'] += $otherInGold;
            }
        }

        $this->analyticUserDailyGoldFlowReportRepository->makeModel()->multiInsertOrUpdate($result);
    }
}
