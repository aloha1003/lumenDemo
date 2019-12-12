<?php

namespace App\Repositories;

use App\Models\UserGoldFlow;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserGoldFlowRepository;

/**
 * Class UserGoldFlowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserGoldFlowRepositoryEloquent extends BaseRepository implements UserGoldFlowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserGoldFlow::class;
    }

}
