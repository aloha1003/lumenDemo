<?php

namespace App\Repositories;

use App\Models\NewAnchor;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\NewAnchorRepository;

/**
 * Class NewAnchorRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NewAnchorRepositoryEloquent extends BaseRepository implements NewAnchorRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NewAnchor::class;
    }

}
