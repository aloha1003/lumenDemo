<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\GameVersionsRepository;
use App\Models\GameVersions;
use App\Validators\GameVersionsValidator;

/**
 * Class GameVersionsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameVersionsRepositoryEloquent extends BaseRepository implements GameVersionsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameVersions::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
