<?php

namespace App\Repositories;

use App\Models\CompanyWithdraw;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\CompanyWithdrawRepository;

/**
 * Class CompanyWithdrawRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyWithdrawRepositoryEloquent extends BaseRepository implements CompanyWithdrawRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CompanyWithdraw::class;
    }

}
