<?php

namespace App\Repositories;

use App\Models\GiftTransactionOrder;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GiftTransactionOrderRepository;

/**
 * Class GiftTransactionOrderRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GiftTransactionOrderRepositoryEloquent extends BaseRepository implements GiftTransactionOrderRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GiftTransactionOrder::class;
    }

}
