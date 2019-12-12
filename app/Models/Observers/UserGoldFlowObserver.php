<?php
namespace App\Models\Observers;

use App\Models\UserGoldFlow;

/**
 * 写入金币流水记录
 */
class UserGoldFlowObserver
{

    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function updated($userModel)
    {
        if ($userModel->goldDiff != 0 && method_exists($userModel, 'getGoldUpdateSourceModel') && $userModel->getGoldUpdateSourceModel()) {
            $this->writeUserGoldFlow($userModel);
        }
    }

    private function writeUserGoldFlow($userModel)
    {
        $userGoldFlowModel = app(UserGoldFlow::class);

        $sourceModel = $userModel->getGoldUpdateSourceModel();
        $userOriginalArray = $userModel->getOriginal();

        if ($userOriginalArray) {
            if (config('app.current_platform', '')) {
                $fromWhichPlatform = config('app.current_platform');
            } else {
                $fromWhichPlatform = config('app.currentenv', 'admin');
            }
            $data = [
                'source_model_name' => get_class($sourceModel),
                'source_model_primary_key_column' => $sourceModel->getKeyName(),
                'source_model_primary_key_id' => $sourceModel->getKey(),
                'user_id' => $userModel->id,
                'gold_origin' => $userOriginalArray['gold'],
                'gold_operation' => $userModel->goldDiff,
                'amount' => goldToCoin($userModel->goldDiff),
                'gold_remain' => $userOriginalArray['gold'] + $userModel->goldDiff,
                'from_which_platform' => $fromWhichPlatform,
                'source_user_id' => platformId(),
            ];
            $userGoldFlowModel->create($data);
        }
    }
}
