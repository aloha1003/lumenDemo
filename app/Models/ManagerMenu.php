<?php
namespace App\Models;

use Encore\Admin\Auth\Database\Menu;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ManagerMenu extends Menu
{
    protected $table = "manager_menu";
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $connection = config('admin.database.connection') ?: config('database.default');
        $this->setConnection($connection);
        $this->setTable(config('manager.database.menu_table'));
    }

    /**
     * A Menu belongs to many roles.
     *
     * @return BelongsToMany
     */
    public function roles(): BelongsToMany
    {
        $pivotTable = config('manager.database.role_menu_table');

        $relatedModel = config('manager.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'menu_id', 'role_id');
    }
}
