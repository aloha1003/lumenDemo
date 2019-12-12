<?php
namespace App\Models\Observers;

//TODO
class AnchorReportObserver
{
    public function creating($model)
    {
        $original = $managerOriginalArray = $managerModel->getOriginal();
        if ($model->report_status != $model::REPORT_STATUS_NO) {
            throw new \Exception(__("anchor_report.report_error_operation_creating"));
        }
    }
    public function updating($model)
    {
        $original = $managerOriginalArray = $managerModel->getOriginal();
        if ($original['report_status'] != $model::REPORT_STATUS_NO) {
            throw new \Exception(__("anchor_report.report_error_operation"));
        }
    }

}
