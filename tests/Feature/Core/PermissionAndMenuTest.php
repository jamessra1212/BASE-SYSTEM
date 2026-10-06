<?php

namespace Tests\Feature\Core;

use App\Core\Models\Menu;
use App\Core\Models\PermissionUserOverride;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionAndMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_perm_middleware_honours_role_and_per_user_overrides(): void
    {
        $permission = Permission::create(['name' => 'manage logs']);
        Role::create(['name' => 'Auditor'])->givePermissionTo($permission);

        $auditor = User::factory()->create();
        $auditor->assignRole('Auditor');
        $outsider = User::factory()->create();

        $this->actingAs($outsider)->get(route('core.logs.index'))->assertForbidden();

        PermissionUserOverride::create(['user_id' => $outsider->id, 'permission_id' => $permission->id, 'access' => 'allow']);
        $this->assertTrue($outsider->fresh()->canAccessPermission('manage logs'));

        PermissionUserOverride::create(['user_id' => $auditor->id, 'permission_id' => $permission->id, 'access' => 'deny']);
        $this->actingAs($auditor)->get(route('core.logs.index'))->assertForbidden();
    }

    public function test_deleting_a_menu_removes_every_descendant_permission(): void
    {
        $root = Menu::create(['name' => 'Reports']);
        $child = Menu::create(['name' => 'Sales', 'parent_id' => $root->id]);
        $grandchild = Menu::create(['name' => 'Sales Destroy', 'parent_id' => $child->id]);

        $names = [$root->permission_name, $child->permission_name, $grandchild->permission_name];
        $this->assertSame(3, Permission::whereIn('name', $names)->count());

        $root->delete();

        $this->assertSame(0, Menu::count());
        $this->assertSame(0, Permission::whereIn('name', $names)->count());
    }

    public function test_role_permission_update_ignores_unknown_permission_names(): void
    {
        Permission::create(['name' => 'manage roles']);
        Permission::create(['name' => 'reports.export']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'Super Admin']));
        $role = Role::create(['name' => 'Editor']);

        $this->actingAs($admin)
            ->put(route('core.roles.permissions.update', $role), [
                'permission_names' => ['reports.export', 'does.not.exist'],
            ])
            ->assertRedirect();

        $this->assertSame(['reports.export'], $role->fresh()->permissions->pluck('name')->all());
    }
}
