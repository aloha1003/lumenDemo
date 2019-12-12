<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class GameAccount.
 *
 * @package namespace App\Models;
 */
class GameVersions extends Model
{
    protected $primaryKey = 'id';

    public function paginate()
    {
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得游戏版本管理列表
        $result = \DB::select(   
            "call $db_game.p_admin_game_versions(0, 0, 99, $page, $perPage);"
        );
        $data = static::hydrate($result);

        // 取得游戏版本管理列表總數
        $result2 = \DB::select(
            "SELECT COUNT(1) AS `count` FROM $db_game.gm_res_versions"
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
            "call $db_game.p_admin_game_versions(0, 0, 99, $page, $perPage);"
        );
        $data = static::hydrate($result);
        return $data[0];
    }

    // 保存提交的form数据
    public function save(array $data = [])
    {
        $id = $data['id'];
        $asseturl = $data['asseturl'];
        $res_ver = $data['res_ver'];
        $is_release = $data['is_release'];

        // print_r($data);
        
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(
            "UPDATE $db_game.gm_res_versions SET `asseturl` = '$asseturl', `res_ver` = '$res_ver', `is_release` = '$is_release' WHERE `id` = '$id';"
        );
        // print_r($result[0]);
    }
}
