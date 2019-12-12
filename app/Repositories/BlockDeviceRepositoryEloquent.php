<?php

namespace App\Repositories;

use App\Models\BlockDevice;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\blockDeviceRepository;

/**
 * Class BlockDeviceRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BlockDeviceRepositoryEloquent extends BaseRepository implements BlockDeviceRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BlockDevice::class;
    }

}
