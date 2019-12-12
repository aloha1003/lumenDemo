<?php

namespace App\Repositories;

use App\Models\AgentNameList;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AgentNameListRepository;

/**
 * Class AgentNameListRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AgentNameListRepositoryEloquent extends BaseRepository implements AgentNameListRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AgentNameList::class;
    }

}
