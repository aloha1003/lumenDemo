<?php

namespace App\Repositories;

use App\Models\UserAuth;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserAuthRepository;

/**
 * Class UserAuthRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserAuthRepositoryEloquent extends BaseRepository implements UserAuthRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserAuth::class;
    }

}
