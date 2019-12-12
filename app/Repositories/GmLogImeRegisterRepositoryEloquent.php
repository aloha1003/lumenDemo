<?php

namespace App\Repositories;

use App\Models\GmLogImeRegister;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\GmLogImeRegisterRepository;

/**
 * Class GmLogImeRegisterRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class GmLogImeRegisterRepositoryEloquent extends BaseRepository implements GmLogImeRegisterRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return GmLogImeRegister::class;
    }

}
