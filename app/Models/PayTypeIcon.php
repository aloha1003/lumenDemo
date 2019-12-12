<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\PayTypeIconObserver;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PayTypeIcon.
 *
 * @package namespace App\Models;
 */
class PayTypeIcon extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['slug', 'icon'];
    const PHOTO_STORE_ROOT = 'pay_type_icon';
    protected $appends = ['slug_title'];
    public function getSlugTitleAttribute($value)
    {
        return sc('support_pay_type')[$this->slug] ?? __('common.not_found_title', ['title' => $this->slug]);
    }
    public function getIconAttribute($value)
    {
        $icon = $value;
        if ($icon) {
            return \CLStorage::url($icon);
        } else {
            return $icon;
        }
    }

    protected static function boot()
    {
        parent::boot();
        static::observe(PayTypeIconObserver::class);
    }
}
