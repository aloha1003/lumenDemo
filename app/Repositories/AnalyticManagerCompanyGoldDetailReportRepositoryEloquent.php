<?php

namespace App\Repositories;

use App\Models\AnalyticManagerCompanyGoldDetailReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticManagerCompanyGoldDetailReportRepository;

/**
 * Class AnalyticManagerCompanyGoldDetailReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticManagerCompanyGoldDetailReportRepositoryEloquent extends BaseRepository implements AnalyticManagerCompanyGoldDetailReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticManagerCompanyGoldDetailReport::class;
    }

}
