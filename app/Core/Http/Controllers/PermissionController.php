<?php

namespace App\Core\Http\Controllers;

use App\Core\DataTables\PermissionsDataTable;
use App\Http\Controllers\Controller;
use App\Core\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    protected array $protectedNames = [
        'manage menus', 'manage roles', 'manage users', 'manage permissions', 'manage access', 'manage settings',
    ];

    public function index(PermissionsDataTable $dataTable)
    {
        return $dataTable->render('admin.permissions.index');
    }

    /**
     * Returns the create/edit modal markup as HTML, loaded via AJAX.
     * Pass ?id= to load an existing permission for editing.
     */
    public function entry(Request $request)
    {
        $permission = $request->filled('id') ? Permission::findOrFail($request->id) : new Permission();
        $menus = Menu::orderBy('name')->get();

        return view('admin.permissions._form', compact('permission', 'menus'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Permission::create($data + ['guard_name' => 'web']);

        return response()->json(['status' => 'success', 'message' => 'Permission created.']);
    }

    public function update(Request $request, Permission $permission)
    {
        if (in_array($permission->name, $this->protectedNames) && $request->name !== $permission->name) {
            return response()->json([
                'status' => 'error',
                'message' => 'Core system permission names can\'t be changed (but its group/menu link can).',
            ], 422);
        }

        $data = $this->validated($request, $permission->id);
        $permission->update($data);

        return response()->json(['status' => 'success', 'message' => 'Permission updated.']);
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

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $request->validate([
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('permissions', 'name')->ignore($ignoreId),
                function ($attribute, $value, $fail) {
                    if (str_starts_with($value, 'menu.')) {
                        $fail('Names starting with "menu." are reserved for Menu Management.');
                    }
                },
            ],
            'menu_id' => ['nullable', 'exists:menus,id'],
        ]);

        // Group is derived, never typed directly: prefer the linked menu's
        // name so the display label always matches where it's nested;
        // otherwise fall back to the part of the name before the first dot.
        $group = $request->filled('menu_id')
            ? Menu::find($request->menu_id)?->name
            : Str::headline(Str::before($request->name, '.') ?: 'General');

        return [
            'name' => $request->name,
            'group' => $group,
            'menu_id' => $request->menu_id ?: null,
        ];
    }
}