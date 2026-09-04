<?php

namespace App\Core\Http\Controllers;

use App\Core\DataTables\RolesDataTable;
use App\Http\Controllers\Controller;
use App\Core\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
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
     * Show the checkbox tree of menus (with related action-level
     * permissions nested under each), plus a fallback flat list for
     * permissions not linked to any menu.
     */
    public function permissions(Role $role)
    {
        $menus = Menu::with('children')->topLevel()->orderBy('order')->get();

        $actionPermissions = Permission::where('name', 'not like', 'menu.%')
            ->orderBy('group')->orderBy('name')->get();

        $permissionsByMenu = $actionPermissions->whereNotNull('menu_id')->groupBy('menu_id');
        $unassignedGrouped = $actionPermissions->whereNull('menu_id')
            ->groupBy(fn ($p) => $p->group ?: 'General');

        $rolePermissionNames = $role->permissions->pluck('name');

        return view('admin.roles.permissions', compact(
            'role', 'menus', 'permissionsByMenu', 'unassignedGrouped', 'rolePermissionNames'
        ));
    }

    /**
     * Save which menus AND which action-level permissions this role has.
     * This fully replaces the role's permission set with whatever was
     * checked on the page — nothing is preserved beyond what's submitted.
     */
    public function updatePermissions(Request $request, Role $role)
    {
        $selectedMenuIds = collect($request->input('menu_ids', []))->map(fn ($id) => (int) $id);
        $selectedMenuPermissionNames = Menu::whereIn('id', $selectedMenuIds)->pluck('permission_name');

        $selectedActionPermissionNames = collect($request->input('permission_names', []));

        $role->syncPermissions($selectedMenuPermissionNames->merge($selectedActionPermissionNames)->unique());

        return back()->with('success', "Permissions updated for role \"{$role->name}\".");
    }
}