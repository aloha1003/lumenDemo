<?php

namespace App\Models;

use App\Models\BaseModel;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticManagerCompanyGoldDetailReport.
 *
 * @package namespace App\Models;
 */
class AnalyticManagerCompanyGoldDetailReport extends BaseModel implements Transformable
{
    use TransformableTrait;
    
    protected $isLog = false;

    public $table = 'analytic_manager_company_gold_detail_report';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 
        'company_id', 
        'start_date', 
        'end_date', 
        'user_id', 
        'nickname', 
        'total_gold_income', 
        'receive_normal_gift', 
        'normal_gift_gold_income',
        'receive_big_gift',
        'big_gift_gold_income',
        'receive_mission_gift',
        'mission_gift_gold_income'
    ];

    public function getLogInfo($column, $id)
    {
        $collection = $this->where($column, $id)->get();

        $data = $collection->first();
        if ($data == null) {
            return '';
        }

        return __('analyticManagerCompanyGoldReport.company_id') . ' : ' . $data->company_id . ', <br>' . 
                __('analyticManagerCompanyGoldReport.user_id') . ' : ' . $data->user_id . ', <br>' . 
                __('analyticManagerCompanyGoldReport.start_date') . ' : ' . $data->start_date . ', <br>' . 
                __('analyticManagerCompanyGoldReport.end_date') . ' : ' . $data->end_date . ', <br>' . 
                __('analyticManagerCompanyGoldReport.total_company_incom') . ' : ' . $data->total_company_income . ', <br>' . 
                __('analyticManagerCompanyGoldReport.total_anchor_incom') . ' : ' . $data->total_anchor_income;
    }

}
