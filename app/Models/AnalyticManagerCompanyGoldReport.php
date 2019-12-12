<?php

namespace App\Models;

use App\Models\BaseModel;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnalyticManagerCompanyGoldReport.
 *
 * @package namespace App\Models;
 */
class AnalyticManagerCompanyGoldReport extends BaseModel implements Transformable
{
    use TransformableTrait;
    
    protected $isLog = false;

    public $table = "analytic_manager_company_gold_report";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id', 
        'start_date', 
        'end_date', 
        'manager_number', 
        'anchor_number', 
        'total_company_gold_income', 
        'total_anchor_gold_income',
    ];

    public function getLogInfo($column, $id)
    {
        $collection = $this->where($column, $id)->get();

        $data = $collection->first();
        if ($data == null) {
            return '';
        }

        return __('analyticDetailCompanyReport.company_id') . ' : ' . $data->company_id . ', <br>' . 
                __('analyticDetailCompanyReport.start_date') . ' : ' . $data->start_date . ', <br>' . 
                __('analyticDetailCompanyReport.end_date') . ' : ' . $data->end_date . ', <br>' . 
                __('analyticDetailCompanyReport.total_company_incom') . ' : ' . $data->total_company_gold_income . ', <br>' . 
                __('analyticDetailCompanyReport.total_anchor_incom') . ' : ' . $data->total_anchor_gold_income;
    }

}
