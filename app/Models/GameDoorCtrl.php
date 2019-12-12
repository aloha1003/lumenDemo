<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class GameDoorCtrl.
 *
 * @package namespace App\Models;
 */
class GameDoorCtrl extends Model
{
    protected $primaryKey = 'gameId';

    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得盈利列表
        $result = \DB::select(   
            "call $db_game.p_admin_cfgdoorctrl_list(0, $page, $perPage);"
        );
        $data = static::hydrate($result);
        
        // 取得盈利列表總數
        $result2 = \DB::select(
            "SELECT COUNT(1) AS `count` FROM $db_game.cfg_door_ctrl;"
        );
        $obj = $result2[0];
        $total = $obj->count;

        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());

        return $paginator;
    }

    // 获取单项数据展示在form中
    static public function findOrFail($id)
    {
        $perPage = 1;
        $page = 1;
        $db_game = env('DB_DATABASE_GAME');
        // $id = 132;

        // 取得帳號列表
        $result = \DB::select(
            "call $db_game.p_admin_cfgdoorctrl_list('$id', $page, $perPage);"
        );
        $data = static::hydrate($result);
        return $data[0];
    }

    // 保存提交的form数据
    public function save(array $data = [])
    {
        $id = $data['id'];
        $gameId = $data['gameId'];
        // $gamename = $data['gamename'];
        $sysWinScale = $data['sysWinScale'];
        $sysTurns = $data['sysTurns'];
        $sysBets = $data['sysBets'];
        $sysWins = $data['sysWins'];
        
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(
            "call $db_game.p_admin_cfgdoorctrl_edit('$id', '$gameId', '$sysWinScale', '$sysTurns', '$sysBets', '$sysWins');"
        );
        // print_r($result[0]);
    }
}
