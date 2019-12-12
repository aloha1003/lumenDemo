<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;

/**
 * Class GameBenzLog.
 *
 * @package namespace App\Models;
 */
class GameBenzLog extends Model
{
    public function paginate()
    {
        $id = Request::get('id', 0);
        $perPage = Request::get('per_page', 20);
        $page = Request::get('page', 1);
        $db_game = env('DB_DATABASE_GAME');

        // 取得賓士日誌
        $result = \DB::select(   
            "call $db_game.p_admin_fcdc_logs('$id', '', '', $page, $perPage);"
        );
        $data = static::hydrate($result);

        // 取得賓士日誌總數
        if($id != 0 ) {
            $result2 = \DB::select(
                "SELECT COUNT(1) AS `count` FROM $db_game.log_benz_result WHERE `benz_id` = '$id';"
            );

        } else {
            $result2 = \DB::select(
                "SELECT COUNT(1) AS `count` FROM $db_game.log_benz_result;"
            );
        }
        $obj = $result2[0];
        $total = $obj->count;

        $paginator = new LengthAwarePaginator($data, $total, $perPage);
        $paginator->setPath(url()->current());

        return $paginator;
    }
}
