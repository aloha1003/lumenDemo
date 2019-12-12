<?php

namespace App\Repositories;

use App\Models\GmLogDailyRewards;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmLogDailyRewardsRepository;

/**
 * Class GmLogDailyRewardsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmLogDailyRewardsRepositoryEloquent extends BaseRepository implements GmLogDailyRewardsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmLogDailyRewards::class;
    }

}
