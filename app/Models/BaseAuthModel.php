<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use App\Models\Observers\CleanCacheObserver;
use App\Models\Observers\ModelLogObserver;
use Encore\Admin\Auth\Database\HasPermissions;
use Encore\Admin\Traits\AdminBuilder;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BaseAuthModel extends Model implements AuthenticatableContract
{

    use Authenticatable, AdminBuilder, HasPermissions;
    protected $isLog = true;
    protected $roleTablePrefix = 'admin';
    protected $rolesModel = '';

    public static function getTableName()
    {
        return with(new static )->getTable();
    }

    public function getIsLog()
    {
        return $this->isLog;
    }
    public static function boot()
    {
        parent::boot();
        static::observe(ModelLogObserver::class);
        static::observe(CleanCacheObserver::class);
    }

    public static function getEmptyModel()
    {
        $column = modelColumn(static::class);
        $default = [];
        if (is_array($column)) {
            $columnKeys = array_keys($column);
            $default = array_fill_keys($columnKeys, '');
        }
        return $default;
    }

    /**
     * A user has and belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('manager.database.role_users_table');

        $relatedModel = $this->rolesModel . '::class';

        return $this->belongsToMany($this->rolesModel, $pivotTable, 'user_id', 'role_id');
    }

}
