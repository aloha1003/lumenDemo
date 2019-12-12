<?php

return [
    'database' => [

        // Database connection for following tables.
        'connection' => '',

        // User tables and model.
        'users_table' => 'manager',
        'users_model' => App\Models\Manager::class,

        // Role table and model.
        'roles_table' => 'manager_roles',
        'roles_model' => App\Models\ManagerRole::class,

        // Permission table and model.
        'permissions_table' => 'manager_permissions',
        'permissions_model' => App\Models\ManagerPermission::class,

        // Menu table and model.
        'menu_table' => 'manager_menu',
        'menu_model' => App\Models\ManagerMenu::class,

        // Pivot table for table above.
        'operation_log_table' => 'manager_operation_log',
        'user_permissions_table' => 'manager_user_permissions',
        'role_users_table' => 'manager_role_users',
        'role_permissions_table' => 'manager_role_permissions',
        'role_menu_table' => 'manger_role_menu',
    ],
];
