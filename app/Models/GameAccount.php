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
class GameAccount extends Model
{
    protected $primaryKey = 'gid';

    public function paginate()
    {
        $id = Request::get('gid', 0);
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(   
            "call $db_game.p_admin_account_list('$id', '', 0, '', '', $page, $perPage, 0, '');"
        );
        $data = static::hydrate($result);

        // 取得帳號列表總數
        $result2 = \DB::select(
            "call $db_game.p_admin_account_total('$id', '', 0, '', '', 0, 0, 0, '');"
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
            "call $db_game.p_admin_account_list('$id', '', 0, '', '', $page, $perPage, 0, '');"
        );
        $data = static::hydrate($result);
        return $data[0];
    }

    // 保存提交的form数据
    public function save(array $data = [])
    {
        $gid = $data['gid'];
        $name = $data['name'];
        $vip = $data['vip'];
        $coin = $data['coin'];
        $point = $data['point'];
        $box = $data['box'];
        $status = $data['status'];
        $type = $data['type'];
        
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(
            "call $db_game.p_admin_edit_account('$gid', '$name', '$vip', '$coin', '$point', '$box', '$status', '$type');"
        );
        // print_r($result[0]);
    }

    public function resetBoxPassword($gid)
    {
        $password = "888888";
        
        $db_game = env('DB_DATABASE_GAME');

        // 取得帳號列表
        $result = \DB::select(
            "call $db_game.p_bk_rest_boxpassword('$gid', '$password');"
        );
        // print_r($result);
    }
}
