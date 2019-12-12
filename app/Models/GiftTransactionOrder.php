<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GiftTransactionOrder.
 *
 * @package namespace App\Models;
 */
class GiftTransactionOrder extends Model implements Transformable
{
    protected $isLog = false;

    use TransformableTrait;
    public $table = 'gift_transaction_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'room_id',
        'give_uid',
        'receive_uid',
        'gift_type_id',
        'hot_value',
        'hot_expired_time',
        'gold_remain',
        'gold_price',
        'anchor_real_receive_gold',
        'company_real_receive_gold',
        'propotion_setting',
    ];
    public static function getTableName()
    {
        return with(new static )->getTable();
    }

    public function getLogInfo($column, $id)
    {
        //TODO
        $collection = $this->where($column, $id)->get();

        $data = $collection->first();
        if ($data == null) {
            return '';
        }
        $baseGiftTypeModel = app(\App\Models\BaseGiftType::class);

        $allGiftTypeModel = $baseGiftTypeModel->getStaticAllData();

        $missionGiftSlug = [];
        $giftSlugToModel = [];
        foreach ($allGiftTypeModel as $giftTypeModel) {
            if ($giftTypeModel->is_mission) {
                $missionGiftSlug[] = $giftTypeModel->type_slug;
            }
            $giftSlugToModel[$giftTypeModel->type_slug] = $giftTypeModel;
        }

        if (in_array($data->gift_type_id, $missionGiftSlug)) {
            return '';
        }

        return __('giftTransactionOrder.give_uid') . ' : ' . $data->give_uid . ', <br>' .
        __('giftTransactionOrder.receive_uid') . ' : ' . $data->receive_uid . ', <br>' .
        __('giftTransactionOrder.gift_name') . ' : ' . $giftSlugToModel[$data->gift_type_id]->name . ', <br>' .
        __('giftTransactionOrder.gift_price') . ' : ' . $giftSlugToModel[$data->gift_type_id]->gold_price . ', <br>' .
        __('giftTransactionOrder.anchor_real_receive_gold') . ' : ' . $data->anchor_real_receive_gold . ', <br>' .
        __('giftTransactionOrder.company_real_receive_gold') . ' : ' . $data->company_real_receive_gold;
    }

}
