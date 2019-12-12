<?php

namespace App\Repositories;

use App\Models\ActivityAd;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ActivityAdRepository;

/**
 * Class ActivityAdRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ActivityAdRepositoryEloquent extends BaseRepository implements ActivityAdRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ActivityAd::class;
    }

}
