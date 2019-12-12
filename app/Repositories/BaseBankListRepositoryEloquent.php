<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\BaseBankListRepository;
use App\Models\BaseBankList;
use App\Validators\BaseBankListValidator;

/**
 * Class BaseBankListRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaseBankListRepositoryEloquent extends BaseRepository implements BaseBankListRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BaseBankList::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
