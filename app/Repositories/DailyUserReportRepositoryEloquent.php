<?php

namespace App\Repositories;

use App\Models\DailyUserReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\dailyUserReportRepository;

/**
 * 每日人数报表
 *
 * @package namespace App\Repositories;
 */
class DailyUserReportRepositoryEloquent extends BaseRepository implements DailyUserReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DailyUserReport::class;
    }

}
