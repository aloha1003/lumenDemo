<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

// 返回 簡訊 實例
class SmsFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sms';
    }
}
