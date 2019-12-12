<?php

namespace App\Repositories;

use App\Models\AnchorInfo;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\anchorInfoRepository;

/**
 * Class AnchorInfoRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnchorInfoRepositoryEloquent extends BaseRepository implements AnchorInfoRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnchorInfo::class;
    }

}
