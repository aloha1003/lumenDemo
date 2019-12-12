<?php

namespace App\Repositories;

use App\Models\AnchorReport;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\AnchorReportRepository;

/**
 * Class AnchorReportRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AnchorReportRepositoryEloquent extends BaseRepository implements AnchorReportRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AnchorReport::class;
    }

}
