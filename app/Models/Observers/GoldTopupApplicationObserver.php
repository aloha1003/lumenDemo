<?php
namespace App\Models\Observers;

class GoldTopupApplicationObserver
{
    public function saving($model)
    {
        $original = $model->getOriginal();
        switch ($model->status) {
            case $model::STATUS_PASS:
            case $model::STATUS_REJECT:
                if (!$original || $original['status'] != $model::STATUS_NO) {
                    throw new \Exception(__('goldTopupApplication.illeagl_gold_topup_operate'));
                }
                break;
            default:
                # code...
                break;
        }
    }
    public function saved($model)
    {
        switch ($model->status) {
            case $model::STATUS_PASS:
                $model->with('user');
                $userCurrentGold = $model->user->gold;
                $model->user->addGold($model->gold, $model);
                break;
            default:
                # code...
                break;
        }
    }
}
