<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\DailyGameBetReportRepository;
use App\Models\DailyGameBetReport;
use App\Validators\DailyGameBetReportValidator;

/**
 * Class DailyGameBetReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DailyGameBetReportRepositoryEloquent extends BaseRepository implements DailyGameBetReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DailyGameBetReport::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
