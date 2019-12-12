<?php
namespace App\Services;

use App\Exceptions\ErrorCode;
use App\Repositories\Interfaces\AgentNameListRepository;
use App\Repositories\Interfaces\AgentTransactionListRepository;
use App\Repositories\Interfaces\AnalyticAgentTransportGoldStatisticRepository;
use App\Repositories\Interfaces\UserConfigRepository;
use App\Repositories\Interfaces\UserRepository;
use App\Services\UserService;
use Carbon\Carbon;

//代理服务
class AgentService
{
    private $userRepository;
    private $agentNameListRepository;
    private $agentTransactionListRepository;
    private $userConfigRepository;

    public function __construct(
        UserRepository $userRepository,
        AgentNameListRepository $agentNameListRepository,
        AgentTransactionListRepository $agentTransactionListRepository,
        UserConfigRepository $userConfigRepository,
        AnalyticAgentTransportGoldStatisticRepository $analyticAgentTransportGoldStatisticRepository) {
        $this->userRepository = $userRepository;
        $this->agentNameListRepository = $agentNameListRepository;
        $this->agentTransactionListRepository = $agentTransactionListRepository;
        $this->userConfigRepository = $userConfigRepository;
        $this->analyticAgentTransportGoldStatisticRepository = $analyticAgentTransportGoldStatisticRepository;
    }

    /**
     * 建立一筆轉帳交易
     *
     * @param int $agentUserId
     * @param int $targetUserId
     * @param int $gold
     * @param string $comment
     *
     * @return array
     */
    public function createTransferGoldTransaction($agentUserId, $targetUserId, $gold, $comment = '')
    {
        // 不能對自己交易
        if ($agentUserId == $targetUserId) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 目標用戶不存在
        $targetUserModel = $this->userRepository->findWhere(['id' => $targetUserId])->first();
        if ($targetUserModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        // 代理用戶不存在
        $agentUerModel = $this->userRepository->findWhere(['id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        $userCnofigModel = $this->userConfigRepository->findWhere(['user_id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }
        // 用戶不是代理
        if ($userCnofigModel->is_agent == false) {
            throw new \Exception(__('response.code.' . ErrorCode::IS_NOT_AGENT), ErrorCode::IS_NOT_AGENT);
        }

        $agnetRealGold = $agentUerModel->gold_cache;

        // 代理金幣不夠
        if ($agnetRealGold < $gold) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_GOLD_NOT_ENOUGH), ErrorCode::USER_GOLD_NOT_ENOUGH);
        }

        // 準備寫入交易紀錄的資料
        $record = [
            'agent_user_id' => $agentUserId,

            'transaction_target_user_id' => $targetUserId,
            'transaction_gold' => $gold,

            'agent_origin_gold' => $agentUerModel->gold,
            'agent_remain_gold' => floor(($agentUerModel->gold - $gold) * 100) / 100,

            'user_origin_gold' => $targetUserModel->gold,
            'user_remain_gold' => floor(($targetUserModel->gold + $gold) * 100) / 100,

            'comment' => $comment,
        ];

        $transactionModel = $this->agentTransactionListRepository->create($record);

        // 更新用戶金幣
        $this->userRepository->addGold($targetUserModel, $gold, $transactionModel);

        // 更新代理金幣
        $this->userRepository->addGold($agentUerModel, -1 * $gold, $transactionModel);

        // 取得統計資料Model
        $statisticModel = $this->analyticAgentTransportGoldStatisticRepository->findWhere([
            'agnet_id' => $agentUserId,
            'tranport_gold_user_id' => $targetUserId,
        ])->first();

        // 將轉出金幣數量累加到統計model裡
        if ($statisticModel == null) {
            $statisticData = [
                'agnet_id' => $agentUserId,
                'tranport_gold_user_id' => $targetUserId,
                'total_transport_gold' => $gold,
            ];
            $this->analyticAgentTransportGoldStatisticRepository->create($statisticData);
        } else {
            $statisticModel->total_transport_gold += $gold;
            $statisticModel->save();
        }

        //回傳交易後的金幣數量
        $result = [
            'agent_gold' => $record['agent_remain_gold'],
            'user_gold' => $record['user_remain_gold'],
        ];

        return $result;
    }

    /**
     * 代理對單一用戶的轉帳紀錄
     *
     * @param int $agentUserId
     * @param int $targetUserId
     *
     * @return array
     */
    public function getTransferHistoryByUserId($agentUserId, $targetUserId)
    {
        // 目標用戶不存在
        $targetUserModel = $this->userRepository->findWhere(['id' => $targetUserId])->first();
        if ($targetUserModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        // 代理用戶不存在
        $agentUerModel = $this->userRepository->findWhere(['id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        $userCnofigModel = $this->userConfigRepository->findWhere(['user_id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }
        // 用戶不是代理
        if ($userCnofigModel->is_agent == false) {
            throw new \Exception(__('response.code.' . ErrorCode::IS_NOT_AGENT), ErrorCode::IS_NOT_AGENT);
        }

        // 取得統計資料Model
        $statisticModel = $this->analyticAgentTransportGoldStatisticRepository->findWhere([
            'agnet_id' => $agentUserId,
            'tranport_gold_user_id' => $targetUserId,
        ])->first();
        // 取得對該用戶的所有轉出金幣數量
        $totalGold = 0;
        if ($statisticModel != null) {
            $totalGold = $statisticModel->total_transport_gold;
        }

        // 取得該用戶所有交易紀錄
        $transactionModelArray = $this->agentTransactionListRepository->findWhere(
            [
                'agent_user_id' => $agentUserId,
                'transaction_target_user_id' => $targetUserId,
            ]
        )->all();

        $result = [
            'history' => [],
            'total' => $totalGold,
        ];
        // 整理和統計資料
        foreach ($transactionModelArray as $transactionModel) {
            $arrayData = $transactionModel->toarray();

            $result['history'][] = [
                'datetime' => $arrayData['created_at'],
                'gold' => $arrayData['transaction_gold'],
                'comment' => $arrayData['comment'],
            ];
            //$result['total'] += $arrayData['transaction_gold'];
        }

        // 依照日期做排序
        usort($result['history'], function ($a, $b) {
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? 1 : -1;
        });
        return $result;
    }

    /**
     * 將用戶移除最愛
     */
    public function unsetUserStar($agentUserId, $targetUserId)
    {
        if ($agentUserId == $targetUserId) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 代理用戶不存在
        $agentUerModel = $this->userRepository->findWhere(['id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        $userCnofigModel = $this->userConfigRepository->findWhere(['user_id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }
        // 用戶不是代理
        if ($userCnofigModel->is_agent == false) {
            throw new \Exception(__('response.code.' . ErrorCode::IS_NOT_AGENT), ErrorCode::IS_NOT_AGENT);
        }

        // 檢查資料是否存在
        $nameListCollection = $this->agentNameListRepository->skipCache(true)->findWhere([
            'agent_user_id' => $agentUserId,
            'trace_user_id' => $targetUserId,
        ]);

        // 若是用戶不在名單中, 回傳錯誤
        if ($nameListCollection->count() == 0) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 檢查要移除最愛的userid是否存在
        $targetUserCollection = $this->userRepository->findWhere(['id' => $targetUserId]);
        if ($targetUserCollection->count() == 0) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        //將資料寫入DB
        $nameListModel = $nameListCollection->first();
        if ($nameListModel->is_star != 0) {
            $nameListModel->is_star = 0;
            $nameListModel->save();
        }
    }

    /**
     * 將用戶設為最愛
     */
    public function setUserStar($agentUserId, $targetUserId)
    {
        if ($agentUserId == $targetUserId) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 代理用戶不存在
        $agentUerModel = $this->userRepository->findWhere(['id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        $userCnofigModel = $this->userConfigRepository->findWhere(['user_id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }
        // 用戶不是代理
        if ($userCnofigModel->is_agent == false) {
            throw new \Exception(__('response.code.' . ErrorCode::IS_NOT_AGENT), ErrorCode::IS_NOT_AGENT);
        }

        // 檢查資料是否存在
        $nameListCollection = $this->agentNameListRepository->skipCache(true)->findWhere([
            'agent_user_id' => $agentUserId,
            'trace_user_id' => $targetUserId,
        ]);

        // 若是用戶不在名單中, 回傳錯誤
        if ($nameListCollection->count() == 0) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 檢查要加到最愛的userid是否存在
        $targetUserCollection = $this->userRepository->findWhere(['id' => $targetUserId]);
        if ($targetUserCollection->count() == 0) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        //將資料寫入DB
        $nameListModel = $nameListCollection->first();
        $nameListModel->is_star = 1;
        $nameListModel->save();

        return ['status' => ErrorCode::OK];
    }

    /**
     * 代理將用戶加到名單中
     *
     * @param int $agentUserId
     * @param int $targetUserId
     */
    public function addUserToNameList($agentUserId, $targetUserId)
    {
        if ($agentUserId == $targetUserId) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 代理用戶不存在
        $agentUerModel = $this->userRepository->findWhere(['id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        $userCnofigModel = $this->userConfigRepository->findWhere(['user_id' => $agentUserId])->first();
        if ($agentUerModel == null) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }
        // 用戶不是代理
        if ($userCnofigModel->is_agent == false) {
            throw new \Exception(__('response.code.' . ErrorCode::IS_NOT_AGENT), ErrorCode::IS_NOT_AGENT);
        }

        // 檢查資料是否存在
        $nameListCollection = $this->agentNameListRepository->skipCache(true)->findWhere([
            'agent_user_id' => $agentUserId,
            'trace_user_id' => $targetUserId,
        ]);

        // 若是用戶已在名單中, 回傳錯誤
        if ($nameListCollection->count() != 0) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }

        // 檢查要加到名單中的userid是否存在
        $targetUserCollection = $this->userRepository->findWhere(['id' => $targetUserId]);
        if ($targetUserCollection->count() == 0) {
            throw new \Exception(__('response.code.' . ErrorCode::USER_NOT_FOUND), ErrorCode::USER_NOT_FOUND);
        }

        //將資料寫入DB
        $data = [
            'agent_user_id' => $agentUserId,
            'trace_user_id' => $targetUserId,
        ];
        $this->agentNameListRepository->create($data);

        return ['status' => ErrorCode::OK];
    }

    /**
     * 代理將用戶從名單中移除
     *
     * @param int $agentUserId
     * @param int $targetUserId
     */
    public function removeUserFromNameList($agentUserId, $targetUserId)
    {
        if ($agentUserId == $targetUserId) {
            throw new \Exception(__('response.code.' . ErrorCode::BAD_REQUEST), ErrorCode::BAD_REQUEST);
        }
        $where = [
            'agent_user_id' => $agentUserId,
            'trace_user_id' => $targetUserId,
        ];
        $this->agentNameListRepository->deleteWhere($where);

        return ['status' => ErrorCode::OK];
    }

    /**
     * 取得名單列表
     *
     * @param int $agentUserId
     *
     * @return array
     */
    public function getUserNameList($agentUserId)
    {
        $userService = app(UserService::class);

        $allModelArray = $this->agentNameListRepository->skipCache(true)->findWhere(['agent_user_id' => $agentUserId])->all();
        $idList = [];

        $idToStarList = [];
        $idToCreatedList = [];
        $idToCreatedSecondList = [];

        foreach ($allModelArray as $allModel) {
            $idList[] = $allModel->trace_user_id;
            $idToStarList[$allModel->trace_user_id] = $allModel->is_star;
            $idToCreatedList[$allModel->trace_user_id] = Carbon::parse($allModel->created_at)->format('Y-m-d');
            $idToCreatedSecondList[$allModel->trace_user_id] = Carbon::parse($allModel->created_at)->timestamp;
        }

        // 取得統計資料Model
        $statisticModelArray = $this->analyticAgentTransportGoldStatisticRepository->findWhere([
            'agnet_id' => $agentUserId,
        ])->keyBy('tranport_gold_user_id')->all();

        // 取得所有用戶的基本資料
        $allUserInfoData = $userService->getUserInfoByIds($idList);

        $allUserDataList = [];
        for ($i = 0; $i < count($allUserInfoData); $i++) {
            $id = $allUserInfoData[$i]['user_id'];

            $info = $allUserInfoData[$i];

            $info['total_transport_gold'] = 0;
            if (isset($statisticModelArray[$id])) {
                $info['total_transport_gold'] = $statisticModelArray[$id]->total_transport_gold;
            }
            $info['is_star'] = $idToStarList[$id];
            $info['created_at'] = $idToCreatedList[$id];
            $info['created_at_second'] = $idToCreatedSecondList[$id];

            $allUserDataList[] = $info;
        }
        usort($allUserDataList, function ($a, $b) {
            if ($a['is_star'] == $b['is_star']) {
                return $a['created_at_second'] < $b['created_at_second'] ? 1 : -1;
            }
            return ($a['is_star'] < $b['is_star']) ? 1 : -1;
        });

        return $allUserDataList;
    }

    /**
     * 用日期區間計算總轉帳金幣數量
     *
     * @param int $agentUserId
     * @param string $startDate
     * @param string $endDate
     *
     * @return array
     */
    public function calculateTotalGoldByDate($agentUserId, $startDate = '', $endDate = '')
    {
        if ($startDate == '' || $endDate == '') {
            $where = [
                'agent_user_id' => $agentUserId,
            ];
        } else {
            //$startDatetime = Carbon::parse($startDate . ' 00:00:00')->addHours(-8)->toDateTimeString();
            //$endDatetime = Carbon::parse($endDate . ' 23:59:59')->addHours(-8)->toDateTimeString();

            $startDatetime = $startDate . ' 00:00:00';
            $endDatetime = $endDate . ' 23:59:59';

            $where = [
                'agent_user_id' => $agentUserId,
                ['created_at', ">=", $startDatetime],
                ['created_at', "<=", $endDatetime],
            ];
        }

        $transactionModelArray = $this->agentTransactionListRepository->findWhere($where)->all();
        $totalGold = 0;
        foreach ($transactionModelArray as $transactionModel) {
            $totalGold += $transactionModel->transaction_gold;
        }
        $totalGold = round(($totalGold) * 100) / 100;

        return ['total_gold' => $totalGold];
    }
}
