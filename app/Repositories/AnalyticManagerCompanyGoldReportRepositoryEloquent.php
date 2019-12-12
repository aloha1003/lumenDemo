<?php

namespace App\Repositories;

use App\Models\AnalyticManagerCompanyGoldReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticManagerCompanyGoldReportRepository;

/**
 * Class AnalyticManagerCompanyGoldReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticManagerCompanyGoldReportRepositoryEloquent extends BaseRepository implements AnalyticManagerCompanyGoldReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticManagerCompanyGoldReport::class;
    }

}
