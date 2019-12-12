<?php

namespace App\Repositories;

use App\Models\GameAccount;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GameAccountRepository;

/**
 * Class GameAccountRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameAccountRepositoryEloquent extends BaseRepository implements GameAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameAccount::class;
    }

}
