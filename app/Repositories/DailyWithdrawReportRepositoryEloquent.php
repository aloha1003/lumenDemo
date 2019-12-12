<?php

namespace App\Repositories;

use App\Models\DailyWithdrawReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\DailyWithdrawReportRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class DailyWithdrawReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DailyWithdrawReportRepositoryEloquent extends BaseRepository implements DailyWithdrawReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DailyWithdrawReport::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
