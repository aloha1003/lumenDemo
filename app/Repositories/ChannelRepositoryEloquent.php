<?php

namespace App\Repositories;

use App\Models\Channel;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ChannelRepository;

/**
 * Class ChannelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ChannelRepositoryEloquent extends BaseRepository implements ChannelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Channel::class;
    }

}
