<?php

namespace App\Repositories;

use App\Models\GmCfgServerList;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmCfgServerListRepository;

/**
 * Class GmCfgServerListRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmCfgServerListRepositoryEloquent extends BaseRepository implements GmCfgServerListRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmCfgServerList::class;
    }

}
