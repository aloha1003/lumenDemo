<?php
namespace App\Models\Observers;

// 处理 用户 累积金币计算
class UserGoldObserver
{

    public function creating($model)
    {
        $model->accumulation_gold_save = $model->gold;
        $model->accumulation_gold_spent = 0;
    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param  User $user
     * @return void
     */
    public function saving($model)
    {
        $this->calGold($model);
    }

    public function calGold($model)
    {
        $originial = $model->getOriginal();
        if ($originial) {
            $diffGolden = $model->gold - $originial['gold'];
            if ($diffGolden > 0) {
                //存钱
                $model->accumulation_gold_save = $model->accumulation_gold_save + $diffGolden;
            } else {
                $model->accumulation_gold_spent = $model->accumulation_gold_spent + abs($diffGolden);
            }
        }
        return $model;
    }
}
