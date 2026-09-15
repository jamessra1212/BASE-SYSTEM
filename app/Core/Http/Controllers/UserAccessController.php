<?php

namespace App\Core\Http\Controllers;

use App\Core\Models\Menu;
use App\Core\Models\MenuUserOverride;
use App\Core\Models\PermissionUserOverride;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserAccessController extends Controller
{
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
        $groupedMenus = collect();
        $unassignedGrouped = collect();
        $menuOverrides = collect();
        $permissionOverrides = collect();

        if ($request->filled('user')) {
            $selectedUser = User::findOrFail($request->user);

            $groupedMenus = Menu::leafMenusGroupedForCards();

            $unassignedGrouped = Permission::where('name', 'not like', 'menu.%')
                ->whereNull('menu_id')
                ->orderBy('group')->orderBy('name')
                ->get()
                ->groupBy(fn ($p) => $p->group ?: 'General');

            $menuOverrides = $selectedUser->menuOverrides()->pluck('access', 'menu_id');
            $permissionOverrides = $selectedUser->permissionOverrides()->pluck('access', 'permission_id');
        }

        return view('admin.users.access', compact(
            'users', 'selectedUser', 'groupedMenus', 'unassignedGrouped',
            'menuOverrides', 'permissionOverrides'
        ));
    }

    /**
     * Per item: the "override" checkbox decides whether an override row
     * exists at all; the "state" checkbox (only meaningful when override
     * is on) decides allow vs deny. Override off -> delete any existing
     * row, back to clean inheritance from the role.
     */
    public function update(Request $request, User $user)
    {
        $groupedMenus = Menu::leafMenusGroupedForCards();
        $allMenuIds = $groupedMenus->flatten()->pluck('id');

        $allPermissionIds = Permission::where('name', 'not like', 'menu.%')->pluck('id');

        foreach ($allMenuIds as $menuId) {
            $overrideOn = $request->boolean("menu_override.$menuId");

            if (! $overrideOn) {
                MenuUserOverride::where('user_id', $user->id)->where('menu_id', $menuId)->delete();
                continue;
            }

            $state = $request->boolean("menu_state.$menuId");
            MenuUserOverride::updateOrCreate(
                ['user_id' => $user->id, 'menu_id' => $menuId],
                ['access' => $state ? 'allow' : 'deny']
            );
        }

        foreach ($allPermissionIds as $permissionId) {
            $overrideOn = $request->boolean("perm_override.$permissionId");

            if (! $overrideOn) {
                PermissionUserOverride::where('user_id', $user->id)->where('permission_id', $permissionId)->delete();
                continue;
            }

            $state = $request->boolean("perm_state.$permissionId");
            PermissionUserOverride::updateOrCreate(
                ['user_id' => $user->id, 'permission_id' => $permissionId],
                ['access' => $state ? 'allow' : 'deny']
            );
        }

        activity()->causedBy(auth()->user())->performedOn($user)->log("updated access overrides for user \"{$user->fullname}\"");

        return back()->with('success', "Access updated for {$user->fullname}.");
    }
}