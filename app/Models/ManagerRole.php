<?php

namespace App\Models;

use App\Models\BaseModel as Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ManagerRole extends Model
{
    protected $fillable = ['name', 'slug'];
    protected $table = 'manager_roles';
    const ROLE_COMPANY_SLUG = 'company';
    const ROLE_MANAGER_SLUG = 'manager';
    /**
     * A role belongs to many users.
     *
     * @return BelongsToMany
     */
    public function administrators(): BelongsToMany
    {
        $pivotTable = config('manager.database.role_users_table');

        $relatedModel = config('manager.database.users_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'user_id');
    }

    /**
     * A role belongs to many permissions.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        $pivotTable = config('manager.database.role_permissions_table');

        $relatedModel = config('manager.database.permissions_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'permission_id');
    }

    /**
     * A role belongs to many menus.
     *
     * @return BelongsToMany
     */
    public function menus(): BelongsToMany
    {
        $pivotTable = config('manager.database.role_menu_table');

        $relatedModel = config('manager.database.menu_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'role_id', 'menu_id');
    }

    /**
     * Check user has permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function can(string $permission): bool
    {
        return $this->permissions()->where('slug', $permission)->exists();
    }

    /**
     * Check user has no permission.
     *
     * @param $permission
     *
     * @return bool
     */
    public function cannot(string $permission): bool
    {
        return !$this->can($permission);
    }

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($model) {
            $model->administrators()->detach();

            $model->permissions()->detach();
        });
    }
}
