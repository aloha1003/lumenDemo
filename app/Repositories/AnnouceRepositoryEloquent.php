<?php

namespace App\Repositories;

use App\Models\Annouce;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnnouceRepository;

/**
 * Class AnnouceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnnouceRepositoryEloquent extends BaseRepository implements AnnouceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Annouce::class;
    }

}
