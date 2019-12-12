<?php

namespace App\Repositories;

use App\Models\LiveRoom;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\LiveRoomRepository;

/**
 * Class LiveRoomRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class LiveRoomRepositoryEloquent extends BaseRepository implements LiveRoomRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return LiveRoom::class;
    }

}
