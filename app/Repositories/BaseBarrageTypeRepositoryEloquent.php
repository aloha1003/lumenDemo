<?php

namespace App\Repositories;

use App\Models\BaseBarrageType;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\BaseBarrageTypeRepository;

/**
 * Class BaseBarrageTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseBarrageTypeRepositoryEloquent extends BaseRepository implements BaseBarrageTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseBarrageType::class;
    }

}
