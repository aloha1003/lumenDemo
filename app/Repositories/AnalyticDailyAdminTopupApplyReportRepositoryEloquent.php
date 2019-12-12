<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyAdminTopupApplyReportRepository;
use App\Models\AnalyticDailyAdminTopupApplyReport;
use App\Validators\AnalyticDailyAdminTopupApplyReportValidator;

/**
 * Class AnalyticDailyAdminTopupApplyReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyAdminTopupApplyReportRepositoryEloquent extends BaseRepository implements AnalyticDailyAdminTopupApplyReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyAdminTopupApplyReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
