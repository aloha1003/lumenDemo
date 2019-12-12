<?php

namespace App\Models\Observers;
use App\Services\GameReleaseService;

class GameReleaseVersionObserver
{
    public function created($model)
    {
        $this->writeCache($model);
    }
    /**
     * 监听数据即将保存的事件。
     *
     * @param  User $user
     * @return void
     */
    public function updated($model)
    {
        $this->writeCache($model);
    }

    public function deleted($model)
    {
        $this->writeCache($model); 
    }

    public function writeCache($model)
    {
        $service = app(GameReleaseService::class);
        $gameData = $service->getGameReleaseDataFromDB();
        $service->writeGameReleaseDataToCache($gameData);
    }
}
