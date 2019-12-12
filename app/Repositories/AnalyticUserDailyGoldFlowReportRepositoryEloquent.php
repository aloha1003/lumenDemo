<?php

namespace App\Repositories;

use App\Models\AnalyticUserDailyGoldFlowReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticUserDailyGoldFlowReportRepository;

/**
 * Class AnalyticUserDailyGoldFlowReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticUserDailyGoldFlowReportRepositoryEloquent extends BaseRepository implements AnalyticUserDailyGoldFlowReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticUserDailyGoldFlowReport::class;
    }

}
