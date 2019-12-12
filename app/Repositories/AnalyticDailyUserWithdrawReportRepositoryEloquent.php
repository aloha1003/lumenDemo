<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnalyticDailyUserWithdrawReportRepository;
use App\Models\AnalyticDailyUserWithdrawReport;
use App\Validators\AnalyticDailyUserWithdrawReportValidator;

/**
 * Class AnalyticDailyUserWithdrawReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticDailyUserWithdrawReportRepositoryEloquent extends BaseRepository implements AnalyticDailyUserWithdrawReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticDailyUserWithdrawReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
