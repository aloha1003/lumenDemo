<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyUserNumberReportRepository;
use App\Models\AnalyticDailyUserNumberReport;
use App\Validators\AnalyticDailyUserNumberReportValidator;

/**
 * Class AnalyticDailyUserNumberReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyUserNumberReportRepositoryEloquent extends BaseRepository implements AnalyticDailyUserNumberReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyUserNumberReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
