<?php

namespace App\Repositories;

use App\Models\GmCfgVipConfig;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmCfgVipConfigRepository;

/**
 * Class GmCfgVipConfigRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmCfgVipConfigRepositoryEloquent extends BaseRepository implements GmCfgVipConfigRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmCfgVipConfig::class;
    }

}
