<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

// 返回 IM 的實例
class IMFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'im';
    }
}
