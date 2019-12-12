<?php

namespace App\Repositories;

use App\Models\UserTopupReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\userTopupReportRepository;

/**
 * Class UserTopupReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTopupReportRepositoryEloquent extends BaseRepository implements UserTopupReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTopupReport::class;
    }

}
