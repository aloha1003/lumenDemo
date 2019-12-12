<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class GameCoinChangeLog.
 *
 * @package namespace App\Models;
 */
class GameCoinChangeLog extends Model
{
    public function paginate()
    {
        $id = Request::get('uid', 0);
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得金币变化日志
        $result = \DB::select(
            "call $db_game.p_admin_coinchange_logs('$id', 0, '', '', $page, $perPage, '', 99, '');"
        );
        $data = static::hydrate($result);

        // 取得金币变化日志總數
        if ($id != 0) {
            $result2 = \DB::select(
                "SELECT count(1) AS `count` FROM $db_game.log_coin_change WHERE `uid` = '$id';"
            );
        } else {
            $result2 = \DB::select(
                "SELECT count(1) AS `count` FROM $db_game.log_coin_change;"
            );
        }
        $obj = $result2[0];
        $total = $obj->count;

        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());

        return $paginator;
    }
}
