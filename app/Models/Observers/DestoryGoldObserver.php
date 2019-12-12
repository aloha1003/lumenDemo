<?php
namespace App\Models\Observers;

class DestoryGoldObserver
{
    public function saving($model)
    {
        //TODO
        $model->admin_id = adminId();
    }
    public function created($model)
    {
        $model->with('user');
        $userCurrentGold = $model->user->gold_cache;
        $model->user->goldUpdateSourceModel = $model;
        if ($userCurrentGold <= $model->gold) {
            throw new \Exception(__('destoryGold.minus_than_current_gold_exception'));
        }
        $model->user->addGold(-1 * $model->gold, $model);

    }
}
