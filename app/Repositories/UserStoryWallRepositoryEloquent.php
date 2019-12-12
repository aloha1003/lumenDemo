<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\UserStoryWallRepository;
use App\Models\UserStoryWall;
use App\Validators\UserStoryWallValidator;

/**
 * Class UserStoryWallRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserStoryWallRepositoryEloquent extends BaseRepository implements UserStoryWallRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserStoryWall::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
