<?php

namespace App\Repositories;

use App\Models\HomePageBanner;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\homePageBannerRepository;

/**
 * Class HomePageBannerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class HomePageBannerRepositoryEloquent extends BaseRepository implements HomePageBannerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return HomePageBanner::class;
    }

}
