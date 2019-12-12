<?php

namespace App\Repositories;

use App\Models\RealNameVerify;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\RealNameVerifyRepository;

/**
 * Class RealNameVerifyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RealNameVerifyRepositoryEloquent extends BaseRepository implements RealNameVerifyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RealNameVerify::class;
    }

}
