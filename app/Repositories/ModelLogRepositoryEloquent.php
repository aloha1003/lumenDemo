<?php

namespace App\Repositories;

use App\Models\ModelLog;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ModelLogRepository;

/**
 * Class ModelLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ModelLogRepositoryEloquent extends BaseRepository implements ModelLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ModelLog::class;
    }

}
