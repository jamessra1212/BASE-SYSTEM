<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\PermissionsDataTable;
use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected array $protectedNames = [
        'manage menus', 'manage roles', 'manage users', 'manage permissions',
    ];

    public function index(PermissionsDataTable $dataTable)
    {
        return $dataTable->render('admin.permissions.index');
    }

    public function entry()
    {
        $menus = Menu::orderBy('name')->get();

        return view('admin.permissions._form', compact('menus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255', 'unique:permissions,name',
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, 'menu.')) {
                        $fail('Names starting with "menu." are reserved for Menu Management.');
                    }
                },
            ],
            'group' => ['nullable', 'string', 'max:100'],
            'menu_id' => ['nullable', 'exists:menus,id'],
        ]);

        $group = $request->filled('group')
            ? $request->group
            : Str::headline(Str::before($request->name, '.') ?: 'General');

        Permission::create([
            'name' => $request->name,
            'group' => $group,
            'menu_id' => $request->menu_id ?: null,
            'guard_name' => 'web',
        ]);

        return response()->json(['status' => 'success', 'message' => 'Permission created.']);
    }

    public function destroy(Permission $permission)
    {
        if (in_array($permission->name, $this->protectedNames)) {
            return response()->json([
                'status' => 'error',
                'message' => 'This is a core system permission and can\'t be deleted.',
            ], 422);
        }

        $permission->delete();

        return response()->json(['status' => 'success', 'message' => 'Permission deleted.']);
    }
}