<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticGoldStatisticsReportRepository;
use App\Models\AnalyticGoldStatisticsReport;
use App\Validators\AnalyticGoldStatisticsReportValidator;

/**
 * Class AnalyticGoldStatisticsReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticGoldStatisticsReportRepositoryEloquent extends BaseRepository implements AnalyticGoldStatisticsReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticGoldStatisticsReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
