<?php

namespace App\Repositories;

use App\Models\UserConfig;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserConfigRepository;

/**
 * Class UserConfigRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserConfigRepositoryEloquent extends BaseRepository implements UserConfigRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserConfig::class;
    }

}
