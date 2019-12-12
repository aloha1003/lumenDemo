<?php
namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cache;

/**
 * Class CacheUserProvider
 * @package App\Auth
 */
class CacheUserProvider extends EloquentUserProvider
{

    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }
    /**
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {

        $user = Cache::get($this->cacheKey($identifier));
        if (!$user) {
            $user = parent::retrieveById($identifier);
            Cache::put($this->cacheKey($identifier), $user, 86400);
        }
        return $user;

    }

    public function cacheKey($identifier)
    {
        return 'auth:user:' . $this->model . "|$identifier";
    }
}
