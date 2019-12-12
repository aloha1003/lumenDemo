<?php

namespace App\Repositories;

use App\Models\AnalyticDailyGameIncomeReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticDailyGameIncomeReportRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AnalyticDailyGameIncomeReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyGameIncomeReportRepositoryEloquent extends BaseRepository implements AnalyticDailyGameIncomeReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyGameIncomeReport::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
