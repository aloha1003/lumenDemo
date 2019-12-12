<?php

namespace App\Repositories;

use App\Models\UserFeedback;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserFeedbackRepository;

/**
 * Class UserFeedbackRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserFeedbackRepositoryEloquent extends BaseRepository implements UserFeedbackRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserFeedback::class;
    }

}
