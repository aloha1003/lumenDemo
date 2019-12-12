<?php

namespace App\Repositories;

use App\Models\GmResVersions;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\gmResVersionsRepository;

/**
 * Class GmResVersionsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmResVersionsRepositoryEloquent extends BaseRepository implements GmResVersionsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmResVersions::class;
    }

}
