<?php

namespace App\Repositories;

use App\Models\GameCoinChangeLog;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GameCoinChangeLogRepository;

/**
 * Class GameCoinChangeLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameCoinChangeLogRepositoryEloquent extends BaseRepository implements GameCoinChangeLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameCoinChangeLog::class;
    }

}
