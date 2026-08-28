<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuUserOverride;
use App\Models\PermissionUserOverride;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserAccessController extends Controller
{
    /**
     * List/search users. Pick one via ?user= to edit their overrides.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q) => $q->where('fname', 'like', "%{$request->q}%")
                ->orWhere('lname', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%"))
            ->orderBy('fname')
            ->paginate(15)
            ->withQueryString();

        $selectedUser = null;
        $menus = collect();
        $overrides = collect();
        $groupedActionPermissions = collect();
        $permissionOverrides = collect();

        if ($request->filled('user')) {
            $selectedUser = User::findOrFail($request->user);

            $menus = Menu::with('children')->topLevel()->orderBy('order')->get();
            $overrides = $selectedUser->menuOverrides()->pluck('access', 'menu_id');

            $groupedActionPermissions = Permission::where('name', 'not like', 'menu.%')
                ->orderBy('group')
                ->orderBy('name')
                ->get()
                ->groupBy(fn ($permission) => $permission->group ?: 'General');
            $permissionOverrides = $selectedUser->permissionOverrides()->pluck('access', 'permission_id');
        }

        return view('admin.users.access', compact(
            'users', 'selectedUser', 'menus', 'overrides',
            'groupedActionPermissions', 'permissionOverrides'
        ));
    }

    /**
     * Save overrides for one user. Two independent override sets, both
     * shaped the same way: [id] = "allow" | "deny" | "inherit", where
     * "inherit" deletes any existing override row for that id.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'overrides'              => ['array'],
            'overrides.*'            => ['in:allow,deny,inherit'],
            'permission_overrides'   => ['array'],
            'permission_overrides.*' => ['in:allow,deny,inherit'],
        ]);

        foreach ($request->input('overrides', []) as $menuId => $access) {
            if ($access === 'inherit') {
                MenuUserOverride::where('user_id', $user->id)->where('menu_id', $menuId)->delete();
                continue;
            }

            MenuUserOverride::updateOrCreate(
                ['user_id' => $user->id, 'menu_id' => $menuId],
                ['access' => $access]
            );
        }

        foreach ($request->input('permission_overrides', []) as $permissionId => $access) {
            if ($access === 'inherit') {
                PermissionUserOverride::where('user_id', $user->id)->where('permission_id', $permissionId)->delete();
                continue;
            }

            PermissionUserOverride::updateOrCreate(
                ['user_id' => $user->id, 'permission_id' => $permissionId],
                ['access' => $access]
            );
        }

        return back()->with('success', "Access updated for {$user->fullname}.");
    }
}
