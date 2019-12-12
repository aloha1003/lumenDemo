<?php
namespace App\Services;

use App\Repositories\Interfaces\UserGoldFlowRepository;
use App\Repositories\Interfaces\UserGoldTransportRecordRepository;
use App\Repositories\Interfaces\UserRepository;

//用户金流服务
class UserGoldFlowService
{
    use \App\Traits\MagicGetTrait;
    private $repository;
    private $userRepository;
    private $userGoldTransportRecordRepository;

    public function __construct(
        UserGoldFlowRepository $repository,
        UserGoldTransportRecordRepository $userGoldTransportRecordRepository,
        UserRepository $userRepository
    ) {
        $this->repository = $repository;

        $this->userRepository = $userRepository;

        $this->userGoldTransportRecordRepository = $userGoldTransportRecordRepository;
    }

    /**
     * 金幣劃帳d
     *
     * @param int $goldInUserId
     * @param int $goldOutUserId
     * @param int $gold
     * @param int $opAdminId
     * @param int $comment
     */
    public function goldTransport($goldInUserId, $goldOutUserId, $gold, $opAdminId, $comment = '')
    {
        $goldInUserModel = $this->userRepository->findWhere(['id' => $goldInUserId])->first();
        if ($goldInUserModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $goldInUserId]));
        }

        $goldOutUserModel = $this->userRepository->findWhere(['id' => $goldOutUserId])->first();
        if ($goldOutUserModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $goldOutUserId]));
        }

        if ($goldOutUserModel->gold < $gold) {
            throw new \Exception(__('cashTransport.gold_out_user_gold_not_enough'));
        }

        $inUserGold = $goldInUserModel->gold;
        $outUserGold = $goldOutUserModel->gold;

        $newInUserGold = $inUserGold + $gold;
        $newOutUserGold = $outUserGold - $gold;

        $recordData = [
            'gold_in_user_id' => $goldInUserId,
            'gold_out_user_id' => $goldOutUserId,
            'transaction_gold' => $gold,
            'gold_in_user_origin_gold' => $inUserGold,
            'gold_in_user_remain_gold' => $newInUserGold,
            'gold_out_user_origin_gold' => $outUserGold,
            'gold_out_user_remain_gold' => $newOutUserGold,
            'op_admin_id' => $opAdminId,
            'comment' => $comment,

        ];
        $recordModel = $this->userGoldTransportRecordRepository->create($recordData);

        $this->userRepository->updateGold($goldInUserModel, $newInUserGold, $recordModel);

        $this->userRepository->updateGold($goldOutUserModel, $newOutUserGold, $recordModel);
    }
}
