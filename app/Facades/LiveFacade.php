<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

// 返回 直播實例
class LiveFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'live';
    }
}
