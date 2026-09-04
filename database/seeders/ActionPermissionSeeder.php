<?php

namespace Database\Seeders;

use App\Core\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ActionPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            'menus.destroy'       => 'Menu Management',
            'roles.destroy'       => 'Roles & Permissions',
            'permissions.destroy' => 'Permissions',
            'user.destroy'        => 'Manage Users',
            'logs.clear'          => 'Activity Logs',
        ];

        foreach ($links as $permissionName => $menuName) {
            $menu = Menu::where('name', $menuName)->first();

            Permission::updateOrCreate(
                ['name' => $permissionName],
                [
                    'guard_name' => 'web',
                    'menu_id'    => $menu?->id,
                    'group'      => $menu?->name,
                ]
            );
        }

        $admin = Role::where('name', 'Admin')->first();
        if ($admin) {
            $admin->givePermissionTo(array_keys($links));
        }
    }
}