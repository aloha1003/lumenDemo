<?php

namespace App\Repositories;

use App\Models\AnalyticAnchorReceiveGoldStatistic;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnalyticAnchorReceiveGoldStatisticRepository;

/**
 * Class AnalyticAnchorReceiveGoldStatisticRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnalyticAnchorReceiveGoldStatisticRepositoryEloquent extends BaseRepository implements AnalyticAnchorReceiveGoldStatisticRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnalyticAnchorReceiveGoldStatistic::class;
    }

}
