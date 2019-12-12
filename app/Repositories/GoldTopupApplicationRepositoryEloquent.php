<?php

namespace App\Repositories;

use App\Models\GoldTopupApplication;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\goldTopupApplicationRepository;

/**
 * Class GoldTopupApplicationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GoldTopupApplicationRepositoryEloquent extends BaseRepository implements GoldTopupApplicationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GoldTopupApplication::class;
    }

}
