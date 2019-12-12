<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AnchorReport.
 *
 * @package namespace App\Models;
 */
class AnchorReport extends Model implements Transformable
{
    use TransformableTrait;

    const REPORT_STATUS_NO = 0;
    const REPORT_STATUS_PASS = 1;
    const REPORT_STATUS_REJECT = 2;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['reason_slug', 'anchor_user_id', 'report_user_id', 'report_status'];

    public function anchor()
    {
        return $this->hasOne('App\Models\User', 'id', 'anchor_user_id');
    }

    public function reporter()
    {
        return $this->hasOne('App\Models\User', 'id', 'report_user_id');
    }

}
