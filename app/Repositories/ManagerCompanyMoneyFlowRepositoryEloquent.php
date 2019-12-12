<?php

namespace App\Repositories;

use App\Models\ManagerCompanyMoneyFlow;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\ManagerCompanyMoneyFlowRepository;

/**
 * Class ManagerCompanyMoneyFlowRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ManagerCompanyMoneyFlowRepositoryEloquent extends BaseRepository implements ManagerCompanyMoneyFlowRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ManagerCompanyMoneyFlow::class;
    }

}
