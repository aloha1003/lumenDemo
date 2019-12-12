<?php

namespace App\Repositories;

use App\Models\RollAd;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\RollAdRepository;

/**
 * Class RollAdRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class RollAdRepositoryEloquent extends BaseRepository implements RollAdRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return RollAd::class;
    }

}
