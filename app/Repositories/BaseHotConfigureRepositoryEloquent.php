<?php

namespace App\Repositories;

use App\Models\BaseHotConfigure;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\BaseHotConfigureRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class BaseHotConfigureRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseHotConfigureRepositoryEloquent extends BaseRepository implements BaseHotConfigureRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseHotConfigure::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
