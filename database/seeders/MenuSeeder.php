<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate (not firstOrCreate) so reruns fix stale routes/icons
        // instead of silently skipping existing rows.
        $dashboard = Menu::updateOrCreate(
            ['name' => 'Dashboard', 'parent_id' => null],
            ['icon' => 'fas fa-tachometer-alt', 'route' => 'sida.main.home', 'order' => 1]
        );

        Menu::updateOrCreate(
            ['name' => 'Manage Users', 'parent_id' => null],
            ['icon' => 'fas fa-user', 'route' => 'sida.main.user', 'order' => 2]
        );

        $settings = Menu::updateOrCreate(
            ['name' => 'Settings', 'parent_id' => null],
            ['icon' => 'fas fa-cog', 'order' => 90]
        );

        Menu::updateOrCreate(
            ['name' => 'Menu Management', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-list', 'route' => 'sida.admin.menus.index', 'order' => 1]
        );

        Menu::updateOrCreate(
            ['name' => 'Roles & Permissions', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-user-shield', 'route' => 'sida.admin.roles.index', 'order' => 2]
        );

        Menu::updateOrCreate(
            ['name' => 'User Access', 'parent_id' => $settings->id],
            ['icon' => 'fas fa-user-lock', 'route' => 'sida.admin.users.access', 'order' => 3]
        );

        // Give Super Admin every menu permission that exists so far.
        $superAdmin = Role::where('name', 'Super Admin')->first();
        if ($superAdmin) {
            $superAdmin->givePermissionTo(Menu::pluck('permission_name'));
        }
    }
}
