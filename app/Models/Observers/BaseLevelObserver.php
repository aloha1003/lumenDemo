<?php

namespace App\Models\Observers;
use App\Models\BaseLevel;

class BaseLevelObserver
{
    
    public function created($model)
    {
        $this->updateBaseLevel($model);
    }
    /**
     * 监听数据即将保存的事件。
     *
     * @param  User $user
     * @return void
     */
    public function updated($model)
    {
        $this->updateBaseLevel($model);
    }

    public function deleted($model)
    {
        $this->updateBaseLevel($model); 
    }
    public function updateBaseLevel($model)
    {
        $all = app(BaseLevel::class)->all()->keyBy('lv')->toArray();
        \Cache::forever($model::CACHE_KEY, $all);
    }
}
