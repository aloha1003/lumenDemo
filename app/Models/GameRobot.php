<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class GameRobot.
 *
 * @package namespace App\Models;
 */
class GameRobot extends Model
{
    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得機器人配置列表
        $result = \DB::select(   
            "SELECT * FROM $db_game.cfg_robot;"
        );
        $data = static::hydrate($result);
       
        // 取得機器人配置列表總數
        $result2 = \DB::select(
            "SELECT COUNT(1) AS `count` FROM $db_game.cfg_robot;"
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

        // 取得帳號列表
        $result = \DB::select(
            "SELECT * FROM $db_game.cfg_robot WHERE `id` = '$id';"
        );
        $data = static::hydrate($result);
        return $data[0];
    }

    // 保存提交的form数据
    public function save(array $data = [])
    {
        $id = $data['id'];
        $srmin = $data['srmin'];
        $srmax = $data['srmax'];
        $mrmin = $data['mrmin'];
        $mrmax = $data['mrmax'];
        $brmin = $data['brmin'];
        $brmax = $data['brmax'];
        $supermin = $data['supermin'];
        $supermax = $data['supermax'];
        
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(
            "call $db_game.p_admin_robot_edit('$id', '$srmin', '$srmax', '$mrmin', '$mrmax', '$brmin', '$brmax', '$supermin', '$supermax');"
        );
        // print_r($result[0]);
    }
}
