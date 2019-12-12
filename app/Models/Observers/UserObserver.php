<?php
namespace App\Models\Observers;

use App\Services\UserService;
use Illuminate\Support\Facades\Cache;

/**
 * 初始化 用户子表 user_config 建立
 */
class UserObserver
{
    public function created($model)
    {
        app(UserService::class)->initUserConfig($model);
    }

    /**
     * @param $model
     */
    public function saved($model)
    {
        $this->updateCache($model);
    }

    public function updateCache($model)
    {

        Cache::put(\Auth::guard('web')->getProvider()->cacheKey($model->id), $model, 86400);
    }

    /**
     * @param $model
     */
    public function deleted($model)
    {
        Cache::forget(\Auth::guard('web')->getProvider()->cacheKey($model->id));
    }
}
