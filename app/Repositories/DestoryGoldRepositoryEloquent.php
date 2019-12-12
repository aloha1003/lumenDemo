<?php

namespace App\Repositories;

use App\Models\DestoryGold;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\destoryGoldRepository;

/**
 * Class DestoryGoldRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DestoryGoldRepositoryEloquent extends BaseRepository implements DestoryGoldRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DestoryGold::class;
    }

}
