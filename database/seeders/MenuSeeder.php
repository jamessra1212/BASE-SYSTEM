<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        Menu::updateOrCreate(
            ['name' => 'Dashboard', 'parent_id' => null],
            ['icon' => 'fas fa-tachometer-alt', 'route' => 'sida.main.home', 'order' => 1]
        );

        $settings = Menu::updateOrCreate(
            ['name' => 'Settings', 'parent_id' => null],
            ['icon' => 'fas fa-cog', 'order' => 90]
        );

        Menu::updateOrCreate(
            ['name' => 'Menu Management', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-list', 'route' => 'core.menus.index', 'order' => 1]
        );

        Menu::updateOrCreate(
            ['name' => 'Roles & Permissions', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-user-shield', 'route' => 'core.roles.index', 'order' => 2]
        );

        Menu::updateOrCreate(
            ['name' => 'User Access', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-user-lock', 'route' => 'core.users.access', 'order' => 3]
        );

        Menu::updateOrCreate(
            ['name' => 'Manage Users', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-user', 'route' => 'core.users.index', 'order' => 4]
        );

        Menu::updateOrCreate(
            ['name' => 'Permissions', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-key', 'route' => 'core.permissions.index', 'order' => 5]
        );

        // Give Super Admin every menu permission that exists so far.
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Menu::pluck('permission_name'));
        }
    }
}