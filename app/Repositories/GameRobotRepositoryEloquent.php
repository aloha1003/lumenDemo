<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\GameRobotRepository;
use App\Models\GameRobot;
use App\Validators\GameRobotValidator;

/**
 * Class GameRobotRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameRobotRepositoryEloquent extends BaseRepository implements GameRobotRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameRobot::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
