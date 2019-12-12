<?php

namespace App\Services;

use App\Repositories\Interfaces\UserLevelAccumulationRepository;
use App\Repositories\Interfaces\UserRepository;

//用户等级服务
class UserLevelService
{
    private $userRepository;
    private $userLevelAccumulationRepository;

    const LIVE_WATCH_SECOND_PER_EXP = 90;
    const LIVE_PLAY_SECOND_PER_EXP = 90;
    const GIVEN_GIFT_GOLD_PER_EXP = 1000;
    const RECEIVE_GIFT_GOLD_PER_EXP = 1000;
    const GAME_BET_GOLD_PER_EXP = 20000;
    const TOPUP_RMB_PER_EXP = 1;

    public function __construct(UserRepository $userRepository, UserLevelAccumulationRepository $userLevelAccumulationRepository)
    {
        $this->userRepository = $userRepository;
        $this->userLevelAccumulationRepository = $userLevelAccumulationRepository;
    }

    /**
     * 增加經驗值 - 儲值
     */
    public function addExpByTopupRMB($userId, $rmb)
    {
        $this->addExpWithAccumulation($userId, $rmb, self::TOPUP_RMB_PER_EXP, 'topup_rmb_for_next_exp');
    }

    /**
     * 增加經驗值 - 送禮
     */
    public function addExpByGivenGift($userId, $giftPrice)
    {
        $this->addExpWithAccumulation($userId, $giftPrice, self::GIVEN_GIFT_GOLD_PER_EXP, 'given_gold_for_next_exp');
    }

    /**
     * 增加經驗值 - 收禮
     */
    public function addExpByReceiveGift($userId, $giftPrice)
    {
        $this->addExpWithAccumulation($userId, $giftPrice, self::RECEIVE_GIFT_GOLD_PER_EXP, 'receive_gold_for_next_exp', '2');
    }

    /**
     * 增加經驗值 - 遊戲下注
     */
    public function addExpByGameBet($userId, $betGold)
    {
        $this->addExpWithAccumulation($userId, $betGold, self::GAME_BET_GOLD_PER_EXP, 'bet_gold_for_next_exp');
    }

    /**
     * 增加經驗值 - 觀看直播
     */
    public function addExpByLiveWatch($userId, $startWatchTime, $endWatchTime)
    {
        $duration = $endWatchTime - $startWatchTime;
        if ($duration < 0) {
            $duration = 0;
        }
        $exp = floor($duration / self::LIVE_WATCH_SECOND_PER_EXP);
        $this->addExp($userId, $exp);
    }

    /**
     * 增加經驗值 - 開直播
     */
    public function addExpByLivePlay($userId, $startPlayTime, $endPlayTime)
    {
        $duration = $endPlayTime - $startPlayTime;
        if ($duration < 0) {
            $duration = 0;
        }
        $exp = floor($duration / self::LIVE_PLAY_SECOND_PER_EXP);
        $this->addExp($userId, $exp);
    }

    /**
     *  增加經驗值
     */
    private function addExp($userId, $exp)
    {
        if ($exp == 0) {
            return;
        }
        $userModel = $this->userRepository->findWhere(['id' => $userId])->first();
        if ($userModel == null) {
            throw new \Exception(__('user.not_found_user', ['user' => $userId]));
        }

        $userModel->level += $exp;
        $userModel->save();
    }

    /**
     * 增加經驗與累積數據
     */
    private function addExpWithAccumulation($userId, $value, $divide, $fieldName, $a = '')
    {
        $exp = floor($value / $divide);
        $remain = $value % $divide;

        if ($exp == 0 && $remain == 0) {
            return;
        }

        if ($exp != 0 && $remain == 0) {
            $this->addExp($userId, $exp);
            return;
        }

        if ($exp == 0 && $remain != 0) {
            $levelAccumulationModel = $this->userLevelAccumulationRepository->findWhere(['user_id' => $userId])->first();
            if ($levelAccumulationModel == null) {
                $levelAccumulationModel = $this->userLevelAccumulationRepository->create(['user_id' => $userId, $fieldName => $remain]);
                return;
            } else {
                $accumulationRemain = $levelAccumulationModel->$fieldName + $remain;
                $exp = floor($accumulationRemain / $divide);
                $remain = $accumulationRemain % $divide;
                if ($exp != 0) {
                    $this->addExp($userId, $exp);
                }
                $levelAccumulationModel->$fieldName = $remain;
                $levelAccumulationModel->save();
                return;
            }
            return;
        }

        if ($exp != 0 && $remain != 0) {

            $levelAccumulationModel = $this->userLevelAccumulationRepository->findWhere(['user_id' => $userId])->first();

            if ($levelAccumulationModel == null) {
                $this->addExp($userId, $exp);

                $levelAccumulationModel = $this->userLevelAccumulationRepository->create(['user_id' => $userId, $fieldName => $remain]);
                return;
            }
            $accumulationRemain = $levelAccumulationModel->$fieldName + $remain;
            $exp = $exp + floor($accumulationRemain / $divide);
            $remain = $accumulationRemain % $divide;

            $this->addExp($userId, $exp);
            $levelAccumulationModel->$fieldName = $remain;
            $levelAccumulationModel->save();
            return;
        }

    }

}
