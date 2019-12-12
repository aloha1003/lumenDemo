<?php

namespace App\Repositories;

use App\Models\SpecialAccount;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\specialAccountRepository;

/**
 * Class SpecialAccountRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SpecialAccountRepositoryEloquent extends BaseRepository implements SpecialAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SpecialAccount::class;
    }

}
