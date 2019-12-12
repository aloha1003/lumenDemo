<?php

namespace App\Models;

use App\Models\AnchorInfo;
use App\Models\BaseModel as Model;
use App\Models\LiveRoom;
use App\Models\Observers\ManagerCompanyGoldFlowObserver;
use App\Models\Observers\SoftDeleteObserver;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Manager.
 *
 * @package namespace App\Models;
 */
class Manager extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    public $goldUpdateSourceModel = null;

    protected $roleTablePrefix = 'manager';
    protected $rolesModel = '\App\Models\ManagerRole';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'status', 'parent_id', 'password', 'role', 'gold'];
    protected $hidden = ['password'];
    protected $appends = ['company_id'];

    const COMPANY_PARENT_ID = 0;

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    const PARENT_ROOT_ID = 0;
    //role
    const ROLE_COMPANY = 1;
    const ROLE_MANAGER = 2;
    const ROLE_ANCHOR = 3;

    // none company, manager
    const NONE_COMPANY_ID = 100;
    const NONE_MANAGER_ID = 101;

    // protected $attributes = [
    //     'parent_id' => self::PARENT_ROOT_ID,
    // ];
    public static function boot()
    {
        parent::boot();
        static::creating(function ($query) {
            $path = [];
            if (!is_numeric($query->parent_id)) {
                $query->parent_id = self::PARENT_ROOT_ID;
            }
            if ($query->parent_id != self::PARENT_ROOT_ID) {
                $parentNode = app(static::class)->findOrFail($query->parent_id);
                $path = json_decode($parentNode->path, true);
            }
            $path[] = $query->parent_id;
            $query->path = json_encode($path);
        });
        static::created(function ($query) {

            if ($query->parent_id) {
                //代表為經紀人
                $role = app($query->rolesModel)->where('slug', '=', $query->rolesModel::ROLE_MANAGER_SLUG)->first();
            } else {
                $role = app($query->rolesModel)->where('slug', '=', $query->rolesModel::ROLE_COMPANY_SLUG)->first();
            }
            $query->roles()->sync($role);

        });
        static::observe(ManagerCompanyGoldFlowObserver::class);
        static::observe(SoftDeleteObserver::class);
    }

    protected function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = bcrypt($value);
        }
        return $this;
    }

    public function getStatusTitle()
    {
        return __('common.status_list');
    }

    public function getRoleTitle()
    {
        return __('manager.role_list');
    }

    public function getCompanyIdAttribute($value)
    {
        $path = json_decode($this->path, true);
        if (json_last_error()) {
            return $this->id;
        } else {
            if (count($path) > 1) {
                return $path[1];
            } else {
                return $this->id;
            }
        }
    }

    public function anchor()
    {
        return $this->hasMany(AnchorInfo::class, 'manager', 'id');
    }

    public function liveRoom()
    {
        return $this->hasMany(LiveRoom::class, 'anchor_info', 'manager_id', 'user_id');
    }

    public function getGoldUpdateSourceModel()
    {
        return $this->goldUpdateSourceModel;
    }

    public function getUserNameAttribute()
    {
        return $this->attributes['name'];
    }

    public function setUserNameAttribute($value)
    {
        $this->attributes['user_name'] = $value;
    }

    public function allPermissions(): Collection
    {
        return $this->roles()->with('permissions')->get()->pluck('permissions')->flatten()->merge($this->permissions);
    }

}
