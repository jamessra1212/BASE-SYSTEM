<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index');
    }

    public function data()
    {
        $roles = Role::withCount('permissions', 'users')->select('roles.*');

        return DataTables::of($roles)
            ->addColumn('actions', fn (Role $role) => '
                <a href="' . route('sida.admin.roles.permissions', $role) . '" class="btn btn-sm btn-outline-primary">Menus</a>
            ')
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function store(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:255', 'unique:roles,name']]);
        Role::create(['name' => $request->name, 'guard_name' => 'web']);

        return back()->with('success', 'Role created.');
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
