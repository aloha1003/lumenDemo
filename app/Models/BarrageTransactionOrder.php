<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BarrageTransactionOrder.
 *
 * @package namespace App\Models;
 */
class BarrageTransactionOrder extends Model implements Transformable
{
    use TransformableTrait;

    protected $isLog = false;

    public $table = 'barrage_transaction_order';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['room_id', 'user_id', 'barrage_type_id', 'message', 'gold_remain'];

    public static function getTableName()
    {
        return with(new static )->getTable();
    }

    public function getLogInfo($column, $id)
    {
        $collection = $this->where($column, $id)->get();

        $data = $collection->first();
        if ($data == null) {
            return '';
        }
        $baseBarrageTypeModel = app(\App\Models\BaseBarrageType::class);

        $allBarrageTypeModel = $baseBarrageTypeModel->getStaticAllData();

        $barrageIdToModel = [];
        foreach ($allBarrageTypeModel as $barrageTypeModel) {
            if ($barrageTypeModel->is_mission) {
                $barrageIdToModel[] = $barrageTypeModel->id;
            }
            $barrageIdToModel[$barrageTypeModel->id] = $barrageTypeModel;
        }

        if (in_array($data->gift_type_id, $missionGiftId)) {
            return '';
        }
        //TODO
        return __('barrageTransactionOrder.room_id') . ' : ' . $data->room_id . ', <br>' .
        __('barrageTransactionOrder.user_id') . ' : ' . $data->user_id . ', <br>' .
        __('barrageTransactionOrder.barrage_name') . ' : ' . $barrageIdToModel[$data->id]->name . ', <br>' .
        __('barrageTransactionOrder.barrage_price') . ' : ' . $barrageIdToModel[$data->id]->gold_price . ', <br>' .
        __('barrageTransactionOrder.created_at') . ' : ' . $data->company_real_receive_gold;
    }

}
