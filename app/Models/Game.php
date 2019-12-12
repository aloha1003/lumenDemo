<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * @SWG\Definition(
 *      definition="Game",
 *      @SWG\Property(
 *          property="id",
 *          type="integer",
 *          format="int32",
 *          description="流水号"
 *      ),
 *      @SWG\Property(
 *          property="name",
 *          type="string",
 *          description="游戏名称"
 *      ),
 *      @SWG\Property(
 *          property="slug",
 *          type="string",
 *          description="游戏识别名称"
 *      ),
 *      @SWG\Property(
 *          property="cover",
 *          type="string",
 *          description="封面"
 *      ),
 *      @SWG\Property(
 *          property="round_cover",
 *          type="string",
 *          description="圓角封面"
 *      ),
 *      @SWG\Property(
 *          property="game_app_id",
 *          type="string",
 *          description="游戏app id "
 *      )
 * )
 */
/**
 * Class Game.
 *
 * @package namespace App\Models;
 */
class Game extends Model implements Transformable
{
    use TransformableTrait;
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status', 'slug', 'cover_ios', 'cover_android', 'rectangle_cover_ios', 'rectangle_cover_android', 'round_cover', 'options', 'game_app_id'];

    const COVER_PATH_PREFIX = 'game_cover';
    const ROUND_COVER_PATH_PREFIX = 'game_round_cover';
    const RECTANGLE_COVER_PATH_PREFIX = 'game_rectangle_cover';

    protected function setOpionsAttribute($value)
    {
        $jsonOptions = json_decode($value);
        if (json_last_error()) {
            return $value;
        } else {
            return json_encode($jsonOptions, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
        }
    }

    /**
     * 取得 IOS 長形图片路径
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T13:05:20+0800
     */
    public function getRectangleCoverIosAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return '';
        }
    }

    /**
     * 取得 Android 長形图片路径
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T13:05:20+0800
     */
    public function getRectangleCoverAndroidAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return '';
        }
    }

    /**
     * 取得 IOS 图片路径
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T13:05:20+0800
     */
    public function getCoverIosAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return '';
        }
    }

    /**
     * 取得 IOS 图片路径
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T13:05:20+0800
     */
    public function getCoverAndroidAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return '';
        }
    }
    /**
     * 取得圓角图片路径
     *
     * @param    [type]                   $value [description]
     *
     * @return   [type]                          [description]
     *
     * @Author   Peter(yj@tiigod.com
     *
     * @DateTime 2019-09-02T13:05:20+0800
     */
    public function getRoundCoverAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return '';
        }
    }

}
