<?php
namespace App\Models\Observers;

use App\Services\AnchorService;


class AnchorInfoObserver
{
    public function created($model)
    {
        $this->writeCache($model);
    }

    public function updated($model)
    {
        $this->writeCache($model);
    }

    public function saved($model)
    {
        $this->writeCache($model);
    }

    /**
     * 將資料寫入cache
     */
    public function writeCache($model)
    {
        $anchorService = app(AnchorService::class);

        $data = [
            'user_id' => $model->user_id,
            'company_id' => $model->company_id,
            'manager_id' => $model->manager_id,
            'front_cover' => $model->front_cover,
            'can_live' => $model->can_live,
            'can_live_history' => $model->can_live_history
        ];

        $anchorService->setAnchorInfoDataToCache($model->user_id, $data);
    }

}
