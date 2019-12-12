<?php

namespace App\Repositories;

use App\Models\SystemConfig;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\SystemConfigRepository;

/**
 * Class SystemConfigRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SystemConfigRepositoryEloquent extends BaseRepository implements SystemConfigRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SystemConfig::class;
    }

}
