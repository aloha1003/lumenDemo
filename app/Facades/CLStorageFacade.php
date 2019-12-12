<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

//檔案上傳
class CLStorageFacade extends Facade
{
    protected static function getFacadeAccessor()
    {

        return 'clstorage';
    }
}
