<?php

namespace App\Models\Observers;

use App\Models\LiveRoom;
use App\Models\User;
use App\Models\UserConfig;
use App\Services\UserService;

/**
 * 更新用户资料快取
 */
class UserInfoObserver
{
    public function created($model)
    {
        if (get_class($model) == LiveRoom::class) {
            $this->writeCache($model);
        }
    }

    public function saved($model)
    {
        if (get_class($model) == User::class || get_class($model) == UserConfig::class) {
            $this->writeCache($model);
        }

        // 當 live room 狀態不是open時, 更新 user info
        if (get_class($model) == LiveRoom::class && $model->status != LiveRoom::STATUS_LIVE) {
            $this->writeCache($model);
        }
    }

    /**
     * 將資料寫入cache
     */
    public function writeCache($model)
    {
        //检查如果只有金额变动
        if (method_exists($model, 'getGoldUpdateSourceModel') && $model->getGoldUpdateSourceModel()) {
            //只有变更金币的话，不做快取更新
            return true;
        }
        $userService = app(UserService::class);

        if (get_class($model) == User::class) {
            //當資料異動時, 寫入cache
            $data = $userService->getUserInfoApiDataFromCollection($model);
            $userService->setUserDataToCache($model->id, $data);
            return;
        }
        if (isset($model->user_id)) {
            $userModel = User::where(['id' => $model->user_id])->get()->first();
            $data = $userService->getUserInfoApiDataFromCollection($userModel);
            $userService->setUserDataToCache($model->user_id, $data);
        }
    }
}
