<?php

namespace App\Repositories;

use App\Models\BlockAnchor;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\blockAnchorRepository;

/**
 * Class BlockAnchorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BlockAnchorRepositoryEloquent extends BaseRepository implements BlockAnchorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BlockAnchor::class;
    }

}
