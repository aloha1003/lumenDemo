<?php
namespace App\Models\Observers;

class SystemConfigObserver
{

    public function created($model)
    {
        $this->updateSystemConfig($model);
    }
    /**
     * 监听数据即将保存的事件。
     *
     * @param  User $user
     * @return void
     */
    public function updated($model)
    {
        $this->updateSystemConfig($model);
    }

    public function deleting($model)
    {
        $this->updateSystemConfig($model);
    }
    public function updateSystemConfig($model)
    {
        resetSystemConfig();
    }
}
