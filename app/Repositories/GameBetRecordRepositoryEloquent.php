<?php

namespace App\Repositories;

use App\Models\GameBetRecord;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GameBetRecordRepository;

/**
 * Class GameBetRecordRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GameBetRecordRepositoryEloquent extends BaseRepository implements GameBetRecordRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GameBetRecord::class;
    }

}
