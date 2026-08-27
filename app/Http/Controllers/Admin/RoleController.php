<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\RolesDataTable;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('admin.roles.index');
    }

    /**
     * Returns the "new role" modal markup as HTML, loaded via AJAX.
     */
    public function entry()
    {
        return view('admin.roles._form');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:255', 'unique:roles,name']]);
        Role::create(['name' => $request->name, 'guard_name' => 'web']);

        return response()->json(['status' => 'success', 'message' => 'Role created.']);
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'Super Admin') {
            return response()->json([
                'status' => 'error',
                'message' => 'The Super Admin role can\'t be deleted.',
            ], 422);
        }

        $role->delete();

        return response()->json(['status' => 'success', 'message' => 'Role deleted.']);
    }

    /**
     * Show the checkbox tree of menus this role currently has permission for.
     */
    public function permissions(Role $role)
    {
        $menus = Menu::with('children')->topLevel()->orderBy('order')->get();
        $rolePermissionNames = $role->permissions->pluck('name');

        return view('admin.roles.permissions', compact('role', 'menus', 'rolePermissionNames'));
    }

    /**
     * Save which menus this role may see. Only touches "menu.*"
     * permissions — any other permissions the role holds are preserved.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $selectedMenuIds = collect($request->input('menu_ids', []))->map(fn ($id) => (int) $id);
        $selectedPermissionNames = Menu::whereIn('id', $selectedMenuIds)->pluck('permission_name');

        $nonMenuPermissions = $role->permissions
            ->reject(fn ($permission) => str_starts_with($permission->name, 'menu.'))
            ->pluck('name');

        $role->syncPermissions($nonMenuPermissions->merge($selectedPermissionNames)->unique());

        return back()->with('success', "Menus updated for role \"{$role->name}\".");
    }
}
