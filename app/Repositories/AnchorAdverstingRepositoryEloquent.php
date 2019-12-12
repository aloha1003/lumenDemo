<?php

namespace App\Repositories;

use App\Models\AnchorAdversting;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\anchorAdverstingRepository;

/**
 * Class AnchorAdverstingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnchorAdverstingRepositoryEloquent extends BaseRepository implements AnchorAdverstingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnchorAdversting::class;
    }

}
