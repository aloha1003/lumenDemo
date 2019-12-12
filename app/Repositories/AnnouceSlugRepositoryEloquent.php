<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\AnnouceSlugRepository;
use App\Models\AnnouceSlug;
use App\Validators\AnnouceSlugValidator;

/**
 * Class AnnouceSlugRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnnouceSlugRepositoryEloquent extends BaseRepository implements AnnouceSlugRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnnouceSlug::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
