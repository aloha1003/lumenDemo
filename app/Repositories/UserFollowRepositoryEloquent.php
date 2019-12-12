<?php

namespace App\Repositories;

use App\Models\UserFollow;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserFollowRepository;

/**
 * Class UserFollowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserFollowRepositoryEloquent extends BaseRepository implements UserFollowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserFollow::class;
    }

}
