<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\Interfaces\CompanyAnchorApplyRepository;
use App\Models\CompanyAnchorApply;
use App\Validators\CompanyAnchorApplyValidator;

/**
 * Class CompanyAnchorApplyRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyAnchorApplyRepositoryEloquent extends BaseRepository implements CompanyAnchorApplyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CompanyAnchorApply::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
