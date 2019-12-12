<?php

namespace App\Repositories;

use App\Models\UserTopupOrderLog;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserTopupOrderLogRepository;

/**
 * Class UserTopupOrderLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserTopupOrderLogRepositoryEloquent extends BaseRepository implements UserTopupOrderLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserTopupOrderLog::class;
    }

}
