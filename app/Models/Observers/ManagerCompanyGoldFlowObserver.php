<?php
namespace App\Models\Observers;
use App\Models\ManagerCompanyGoldFlow;

class ManagerCompanyGoldFlowObserver
{

    /**
     * Handle the post "updated" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function updated($managerModel)
    {
        $diffGolden = $this->getDiffGold($managerModel);
        if ($diffGolden != 0) {
            if ($managerModel->getGoldUpdateSourceModel()) {
                $this->writeManagerGoldFlow($managerModel);
            } else {
                throw new \Exception(__('user.invalid_update_gold'));
            }
        }
    }

    private function getDiffGold($managerModel)
    {
        $managerOriginalArray = $managerModel->getOriginal();
        $diffGolden = $managerModel->gold - $managerOriginalArray['gold'];
        return $diffGolden;
    }

    private function writeManagerGoldFlow($managerModel)
    {   
        $managerCompanyGoldFlowModel = app(ManagerCompanyGoldFlow::class);

        $sourceModel = $managerModel->getGoldUpdateSourceModel();
        $managerOriginalArray = $managerModel->getOriginal();

        if ($managerOriginalArray) {
            $diffGolden = $this->getDiffGold($managerModel);

            $data = [
                'source_model_name' => get_class($sourceModel),   
                'source_model_primary_key_column' => $sourceModel->getKeyName(),   
                'source_model_primary_key_id' => $sourceModel->getKey(),
    
                'company_id'  => $managerModel->id,
                'gold_origin'  => $managerOriginalArray['gold'],
                'gold_operation'  => $diffGolden,
                'gold_remain'  => $managerModel->gold,
            ];
            $managerCompanyGoldFlowModel->create($data);
        }
    }
}
