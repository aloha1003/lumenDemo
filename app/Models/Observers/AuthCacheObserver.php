<?php
namespace App\Models\Observers;

use Illuminate\Support\Facades\Cache;

/**
 * Model observer
 */
class AuthCacheObserver
{
    /**
     * @param $model
     */
    public function saved($model)
    {
        $this->updateCache($model);
    }
    /**
     * @param $model
     */
    public function deleted($model)
    {
        Cache::forget(\Auth::guard('user_auth')->getProvider()->cacheKey($model->user_id));
    }
    /**
     * @param $model
     */
    public function restored($model)
    {
        $this->updateCache($model);
    }
    /**
     * @param $model
     */
    public function retrieved($model)
    {
        $this->updateCache($model);
    }

    public function updateCache($model)
    {
        Cache::put(\Auth::guard('user_auth')->getProvider()->cacheKey($model->user_id), $model, 86400);
    }
}
