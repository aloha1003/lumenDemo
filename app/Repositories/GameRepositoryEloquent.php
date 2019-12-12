<?php

namespace App\Repositories;

use App\Models\Game;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GameRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class GameRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameRepositoryEloquent extends BaseRepository implements GameRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Game::class;
    }

      

}
