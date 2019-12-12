<?php

namespace App\Repositories;

use App\Models\AnchorAdSlug;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnchorAdSlugRepository;

/**
 * Class AnchorAdSlugRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnchorAdSlugRepositoryEloquent extends BaseRepository implements AnchorAdSlugRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnchorAdSlug::class;
    }

}
