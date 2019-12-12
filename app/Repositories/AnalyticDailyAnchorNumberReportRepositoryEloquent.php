<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyAnchorNumberReportRepository;
use App\Models\AnalyticDailyAnchorNumberReport;
use App\Validators\AnalyticDailyAnchorNumberReportValidator;

/**
 * Class AnalyticDailyAnchorNumberReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyAnchorNumberReportRepositoryEloquent extends BaseRepository implements AnalyticDailyAnchorNumberReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyAnchorNumberReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
