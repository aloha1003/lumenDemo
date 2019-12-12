<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\GameBenzLogRepository;
use App\Models\GameBenzLog;
use App\Validators\GameBenzLogValidator;

/**
 * Class GameBenzLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameBenzLogRepositoryEloquent extends BaseRepository implements GameBenzLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameBenzLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
