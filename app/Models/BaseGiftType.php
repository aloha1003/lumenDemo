<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\EloquentSortable\Sortable;
use App\Models\EloquentSortable\SortableTrait;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BaseGiftType.
 *
 * @package namespace App\Models;
 */
class BaseGiftType extends Model implements Transformable, Sortable
{
    use TransformableTrait;
    use SortableTrait;

    const IMAGE_PATH_PREFIX = 'gift/image';
    const SVGA_PATH_PREFIX = 'gift/svga';

    public $sortable = [
        'order_column_name' => 'weight',
        'sort_when_creating' => true,
    ];

    protected $isLog = false;
    public $table = 'base_gift_type';

    private static $allModelData;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type_slug',
        'image',
        'svg',
        'gold_price',
        'comment',
        'weight',
        'hot_value',
        'hot_time',
        'is_big',
        'is_prop',
        'is_mission',
        'onshow',
        'propotion_list',
    ];

    public static function getDefaultData()
    {
        return [
            'name' => '',
            'image' => '',
            'svg' => '',
            'gold_price' => 0,
            'comment' => '',
            'weight' => 1,
            'hot_value' => 0,
            'hot_time' => 0,
            'is_big' => 0,
            'is_prop' => 0,
            'is_mission' => 0,
            'onshow' => 1,
            'propotion_list' => '[{"receive_times":0,"anchor_propotion":5,"company_propotion":20}]',
        ];

    }

    public function getStaticAllData()
    {
        if (self::$allModelData == null) {
            self::$allModelData = $this->all();
        }
        return self::$allModelData;
    }

    public function getPropotionListAttribute($value)
    {
        return array_values(json_decode($value, true) ?: []);
    }

    public function setPropotionListAttribute($value)
    {
        $this->attributes['propotion_list'] = json_encode(array_values($value));
    }

    public function getImageAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }

    public function getSvgAttribute($value)
    {
        $photo = $value;
        if ($photo) {
            return \CLStorage::url($photo);
        } else {
            return $photo;
        }
    }

}
