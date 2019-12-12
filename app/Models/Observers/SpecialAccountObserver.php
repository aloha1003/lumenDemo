<?php
namespace App\Models\Observers;

class SpecialAccountObserver
{
    public function saved($model)
    {
        $original = $model->getOriginal();
        if ($original) {
            $preKey = $model->prettyIdKey($original['user_id']);
            \Cache::forget($preKey);
        }
        $key = $model->prettyIdKey($model->user_id);
        \Cache::forever($key, $model->account);
    }
}
