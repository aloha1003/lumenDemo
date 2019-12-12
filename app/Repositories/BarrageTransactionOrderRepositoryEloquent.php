<?php

namespace App\Repositories;

use App\Models\BarrageTransactionOrder;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\BarrageTransactionOrderRepository;

/**
 * Class BarrageTransactionOrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BarrageTransactionOrderRepositoryEloquent extends BaseRepository implements BarrageTransactionOrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BarrageTransactionOrder::class;
    }

}
