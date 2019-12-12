<?php
namespace App\Auth;

use App\Models\UserAuth;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Support\Facades\Cache;

/**
 * Class CacheUserProvider
 * @package App\Auth
 */
class CacheUserAuthProvider extends EloquentUserProvider
{

    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, UserAuth::class);
    }
    /**
     * @param mixed $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        return Cache::get($this->cacheKey($identifier)) ?? parent::retrieveById($identifier);
    }

    public function cacheKey($identifier)
    {
        return 'auth:user_auth:' . $this->model . "|$identifier";
    }
}
