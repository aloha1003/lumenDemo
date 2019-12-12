<?php

namespace App\Repositories;

use App\Models\UserChatBlackList;
use App\Repositories\ExtendBaseRepository as BaseRepository;
use App\Repositories\Interfaces\UserChatBlackListRepository;

/**
 * Class UserChatBlackListRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserChatBlackListRepositoryEloquent extends BaseRepository implements UserChatBlackListRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserChatBlackList::class;
    }

}
