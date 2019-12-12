<?php

namespace App\Repositories;

use App\Models\AnalyticAgentTransportGoldStatistic;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticAgentTransportGoldStatisticRepository;

/**
 * Class AnalyticAgentTransportGoldStatisticRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticAgentTransportGoldStatisticRepositoryEloquent extends BaseRepository implements AnalyticAgentTransportGoldStatisticRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticAgentTransportGoldStatistic::class;
    }

}
