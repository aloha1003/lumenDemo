<?php

namespace App\Repositories;

use App\Models\FrontPageAdmin;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\frontPageAdminRepository;

/**
 * Class FrontPageAdminRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FrontPageAdminRepositoryEloquent extends BaseRepository implements FrontPageAdminRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FrontPageAdmin::class;
    }

}
