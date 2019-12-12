<?php

namespace App\Models\Observers;
use App\Services\AppReleaseService;

class AppReleaseVersionObserver
{
    public function created($model)
    {
        $this->writeCache($model);
    }

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
        $keyCode = $model->channel_key_code;
        if ($keyCode == null || $keyCode == '') {
            $keyCode = $model->key_code;
        }
        if ($keyCode == null || $keyCode == '') {
            return;
        }
        $service = app(AppReleaseService::class);
        $appData = $service->getAppReleaseDataFromDB($keyCode);
        $service->writeAppReleaseDataToCache($keyCode, $appData);
    }
}
