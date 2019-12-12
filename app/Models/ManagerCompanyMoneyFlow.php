<?php

namespace App\Models;

use App\Contracts\SourceModelAble;
use App\Traits\SourceModelTrait;
use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ManagerCompanyMoneyFlow.
 *
 * @package namespace App\Models;
 */
class ManagerCompanyMoneyFlow extends Model implements Transformable, SourceModelAble
{
    protected $isLog = false;

    use TransformableTrait;
    use SourceModelTrait;
    public $table = 'manager_company_money_flow';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'company_id',
        'rmb',
        'source_model_name',
        'source_model_primary_key_column',
        'source_model_primary_key_id',
    ];

}
