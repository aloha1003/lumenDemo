<?php

namespace App\Repositories;

use App\Models\BaseUserType;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\BaseUserTypeRepository;

/**
 * Class BaseUserTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseUserTypeRepositoryEloquent extends BaseRepository implements BaseUserTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseUserType::class;
    }

}
