<?php

namespace App\Repositories;

use App\Models\ManagerCompanyGoldFlow;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ManagerCompanyGoldFlowRepository;

/**
 * Class ManagerCompanyGoldFlowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ManagerCompanyGoldFlowRepositoryEloquent extends BaseRepository implements ManagerCompanyGoldFlowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ManagerCompanyGoldFlow::class;
    }

}
