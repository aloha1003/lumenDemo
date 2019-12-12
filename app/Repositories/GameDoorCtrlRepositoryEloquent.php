<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\GameDoorCtrlRepository;
use App\Models\GameDoorCtrl;
use App\Validators\GameDoorCtrlValidator;

/**
 * Class GameDoorCtrlRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameDoorCtrlRepositoryEloquent extends BaseRepository implements GameDoorCtrlRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameDoorCtrl::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
