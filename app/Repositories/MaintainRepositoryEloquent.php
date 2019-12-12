<?php

namespace App\Repositories;

use App\Models\Maintain;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\MaintainRepository;

/**
 * Class MaintainRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MaintainRepositoryEloquent extends BaseRepository implements MaintainRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Maintain::class;
    }

}
