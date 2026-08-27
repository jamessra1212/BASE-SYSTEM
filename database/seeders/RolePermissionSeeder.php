<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Non-menu, "can do this action" permissions. Add your own as needed.
        $systemPermissions = [
            'manage roles',
            'manage menus',
            'manage users',
        ];

        foreach ($systemPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $admin      = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Super Admin gets everything, including every menu permission,
        // and this stays true automatically as new menus are created
        // because we check via Gate::before in AuthServiceProvider (see README).
        $admin->givePermissionTo($systemPermissions);

        // Assign the Super Admin role to the first user, if one exists,
        // so you're not locked out after migrating.
        $firstUser = \App\Models\User::first();
        if ($firstUser && ! $firstUser->hasRole('Super Admin')) {
            $firstUser->assignRole($superAdmin);
        }
    }
}
