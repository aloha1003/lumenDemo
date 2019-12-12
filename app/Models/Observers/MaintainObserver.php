<?php
namespace App\Models\Observers;

class MaintainObserver
{
    public function saved($model)
    {
        $this->updateMaintain($model);
    }

    public function created($model)
    {
        $this->updateMaintain($model);
    }
    /**
     * 监听数据即将保存的事件。
     *
     * @param  User $user
     * @return void
     */
    public function updated($model)
    {
        $this->updateMaintain($model);
    }

    public function deleted($model)
    {
        $this->updateMaintain($model);
    }
    public function updateMaintain($model)
    {
        \Cache::forever($model::CACHE_KEY, $model->toArray());
    }
}
