<?php

namespace App\Repositories;

use App\Models\SpecialUser;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\specialUserRepository;

/**
 * Class SpecialUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SpecialUserRepositoryEloquent extends BaseRepository implements SpecialUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SpecialUser::class;
    }

}
