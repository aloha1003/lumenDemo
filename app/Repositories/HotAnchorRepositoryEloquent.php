<?php

namespace App\Repositories;

use App\Models\HotAnchor;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\hotAnchorRepository;

/**
 * Class HotAnchorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class HotAnchorRepositoryEloquent extends BaseRepository implements HotAnchorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return HotAnchor::class;
    }

}
