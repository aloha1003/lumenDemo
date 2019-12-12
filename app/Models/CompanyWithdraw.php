<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyWithdraw.
 *
 * @package namespace App\Models;
 */
class CompanyWithdraw extends Model implements Transformable
{
    use TransformableTrait;

    public $table = 'company_withdraw';

    const STATUS_WAITING = 0;
    const STATUS_HANDLING = 1;
    const STATUS_FINISH = 2;
    const STATUS_REJECT = 3;

    const COMPANY_WITHDRAW_SELF = 0;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'target_anchor_id',
        'withdraw_gold',
        'payment_account',
        'withdraw_rmb',
        'real_withdraw_rmb',
        'status',
        'company_comment',
        'csr_comment',
        'admin_id',
        'payment_channels_slug',
        'fee_type',
        'fee',
        'cost',
    ];

    public function getLogInfo($column, $id)
    {
        $collection = $this->where($column, $id)->get();

        $data = $collection->first();
        if ($data == null) {
            return '';
        }

        $display = modelColumn('managerReport')['waiting_status'];
        switch ($data->status) {
            case CompanyWithdraw::STATUS_WAITING:
                $display = modelColumn('managerReport')['waiting_status'];
                break;
            case CompanyWithdraw::STATUS_HANDLING:
                $display = modelColumn('managerReport')['handling_status'];
                break;
            case CompanyWithdraw::STATUS_FINISH:
                $display = modelColumn('managerReport')['finish_status'];
                break;
            case CompanyWithdraw::STATUS_REJECT:
                $display = modelColumn('managerReport')['reject_status'];
                break;
        }
        //TODO
        return __('companyWithdraw.status') . ' : ' . $display . ', <br>' .
        __('companyWithdraw.withdraw_gold') . ' : ' . $data->withdraw_gold . ', <br>' .
        __('companyWithdraw.withdraw_rmb') . ' : ' . $data->withdraw_rmb . ', <br>' .
        __('companyWithdraw.real_withdraw_rmb') . ' : ' . $data->real_withdraw_rmb . ', <br>';
    }
}
