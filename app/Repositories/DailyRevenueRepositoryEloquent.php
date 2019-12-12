<?php

namespace App\Repositories;

use App\Models\DailyRevenue;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\DailyRevenueRepository;

/**
 * Class DailyRevenueRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DailyRevenueRepositoryEloquent extends BaseRepository implements DailyRevenueRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DailyRevenue::class;
    }

}
