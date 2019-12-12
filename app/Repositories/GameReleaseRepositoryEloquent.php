<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\GameReleaseRepository;
use App\Models\GameRelease;
use App\Validators\GameReleaseValidator;

/**
 * Class GameReleaseRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameReleaseRepositoryEloquent extends BaseRepository implements GameReleaseRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameRelease::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
