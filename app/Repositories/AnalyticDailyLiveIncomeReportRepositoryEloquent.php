<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyLiveIncomeReportRepository;
use App\Models\AnalyticDailyLiveIncomeReport;
use App\Validators\AnalyticDailyLiveIncomeReportValidator;

/**
 * Class AnalyticDailyLiveIncomeReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyLiveIncomeReportRepositoryEloquent extends BaseRepository implements AnalyticDailyLiveIncomeReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyLiveIncomeReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
