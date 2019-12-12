<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\GoldTopupApplicationObserver;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GoldTopupApplication.
 *
 * @package namespace App\Models;
 */
class GoldTopupApplication extends Model implements Transformable
{
    use TransformableTrait;

    //通过状态
    const STATUS_NO = 0;
    const STATUS_PASS = 1;
    const STATUS_REJECT = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'user_id', 'gold', 'comment', 'admin_id', 'application_admin_id'];

    public static function boot()
    {
        parent::boot();
        static::observe(GoldTopupApplicationObserver::class);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(config('admin.database.users_model'), 'admin_id');
    }

    public function application_admin()
    {
        return $this->belongsTo(config('admin.database.users_model'), 'application_admin_id');
    }

}
