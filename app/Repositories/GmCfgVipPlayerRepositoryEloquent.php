<?php

namespace App\Repositories;

use App\Models\GmCfgVipPlayer;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmCfgVipPlayerRepository;

/**
 * Class GmCfgVipPlayerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmCfgVipPlayerRepositoryEloquent extends BaseRepository implements GmCfgVipPlayerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmCfgVipPlayer::class;
    }

}
