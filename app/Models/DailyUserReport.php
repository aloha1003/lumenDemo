<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class DailyUserReport.
 *
 * @package namespace App\Models;
 */
class DailyUserReport extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['date', 'login_user_count', 'new_users_count', 'hour_00', 'hour_01', 'hour_02', 'hour_03', 'hour_04', 'hour_05', 'hour_06', 'hour_07', 'hour_08', 'hour_09', 'hour_10', 'hour_11', 'hour_12', 'hour_13', 'hour_14', 'hour_15', 'hour_16', 'hour_17', 'hour_18', 'hour_19', 'hour_20', 'hour_21', 'hour_22', 'hour_23',
    ];

    protected $appends = ['max_hour'];

    /**
     * 取得最多人数的数量
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T13:14:50+0800
     */
    public function getMaxHourAttribute($value)
    {
        $hours = [];
        for ($i = 0; $i < 24; $i++) {
            $hourAttrName = self::getHourColumn($i);
            if (isset($this->$hourAttrName)) {
                $hours[] = $this->$hourAttrName;
            }
        }
        if ($hours) {
            return max($hours);
        } else {
            return 0;
        }
    }

    /**
     * 取得小时对应的栏位名称
     *
     * @param    int                   $hour 当时的小时
     *
     * @return   [type]                         [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-11-05T14:16:55+0800
     */
    public static function getHourColumn($hour)
    {
        $number = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $hourAttrName = 'hour_' . $number;
        return $hourAttrName;
    }

}
