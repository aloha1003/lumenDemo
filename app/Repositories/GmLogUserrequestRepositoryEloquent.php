<?php

namespace App\Repositories;

use App\Models\GmLogUserrequest;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\gmLogUserrequestRepository;

/**
 * Class GmLogUserrequestRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmLogUserrequestRepositoryEloquent extends BaseRepository implements GmLogUserrequestRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmLogUserrequest::class;
    }

}
