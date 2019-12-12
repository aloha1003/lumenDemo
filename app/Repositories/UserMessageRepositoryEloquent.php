<?php

namespace App\Repositories;

use App\Models\UserMessage;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\userMessageRepository;

/**
 * Class UserMessageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserMessageRepositoryEloquent extends BaseRepository implements UserMessageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserMessage::class;
    }

}
