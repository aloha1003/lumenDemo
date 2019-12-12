<?php

namespace App\Repositories;

use App\Models\UserLoginRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\userLoginRecordRepository;

/**
 * Class UserLoginRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserLoginRecordRepositoryEloquent extends BaseRepository implements UserLoginRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserLoginRecord::class;
    }

}
