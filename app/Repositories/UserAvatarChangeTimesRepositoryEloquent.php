<?php

namespace App\Repositories;

use App\Models\UserAvatarChangeTimes;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserAvatarChangeTimesRepository;

/**
 * Class UserAvatarChangeTimesRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserAvatarChangeTimesRepositoryEloquent extends BaseRepository implements UserAvatarChangeTimesRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserAvatarChangeTimes::class;
    }

}
