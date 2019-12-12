<?php
namespace App\Models\Observers;

class WithDrawGoldObserver
{

    public function saved($model)
    {
        //TODO
        $originalArray = $model->getOriginal();
        // 申訴狀態修改
        if ($originalArray) {
            if (($model->status == $originalArray['status']) && ($model->appeal_status != $originalArray['appeal_status'])) {
                return;
            }
        }

        switch ($model->status) {
            case $model::STATUS_NO:
                if ($originalArray) {
                    //正常新增是不会进来的
                    throw new \Exception(__('withDrawGoldApply.illegal_opeation'));
                }
                $model->with('user');
                $userCurrentGold = $model->user->gold;
                if ($userCurrentGold <= $model->gold) {
                    throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                }
                $model->user->addGold(-1 * $model->gold, $model);
                break;
            case $model::STATUS_PASS:
                if ($originalArray['status'] != $model::STATUS_PROCESSING) {
                    //前一状态必得为送审中
                    throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                }
                //TODO
                //不扣钱，但是可能发通知

                break;
            case $model::STATUS_PROCESSING:
                if ($originalArray['status'] != $model::STATUS_NO) {
                    //前一状态必得为送审中
                    throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                }
                break;
            case $model::STATUS_REJECT:
                if (($originalArray['status'] == $model::STATUS_CANCEL) || ($originalArray['status'] == $model::STATUS_PASS)) {
                    //如果之前状态是已取消，或是已通过，就不能再送出
                    throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                }
                //要把预扣的钱，退回去
                $model->with('user');
                $model->user->addGold($model->gold, $model);
                break;
            case $model::STATUS_CANCEL:
                if ($originalArray['status'] != $model::STATUS_NO) {
                    //前一状态必得为未审核
                    throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                }
                //要把预扣的钱，退回去
                $model->with('user');
                $model->user->addGold($model->gold, $model);
                break;
            default:
                throw new \Exception(__('withDrawGoldApply.minus_than_current_gold_exception'));
                break;
        }
    }
}
