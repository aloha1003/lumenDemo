<?php

namespace App\Repositories;

use App\Models\GmAccountInfo;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmAccountInfoRepository;

/**
 * Class GmAccountInfoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmAccountInfoRepositoryEloquent extends BaseRepository implements GmAccountInfoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmAccountInfo::class;
    }

}
