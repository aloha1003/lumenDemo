<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\DestoryGoldObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class DestoryGold.
 *
 * @package namespace App\Models;
 */
class DestoryGold extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['gold', 'reason', 'user_id', 'admin_id'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(config('admin.database.users_model'), 'admin_id');
    }

    public static function boot()
    {
        parent::boot();
        static::observe(DestoryGoldObserver::class);
    }

}
