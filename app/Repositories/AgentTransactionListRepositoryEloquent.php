<?php

namespace App\Repositories;

use App\Models\AgentTransactionList;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AgentTransactionListRepository;

/**
 * Class AgentTransactionListRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AgentTransactionListRepositoryEloquent extends BaseRepository implements AgentTransactionListRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AgentTransactionList::class;
    }

}
