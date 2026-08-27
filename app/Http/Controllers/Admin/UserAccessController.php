<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuUserOverride;
use App\Models\User;
use App\Services\MenuService;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    /**
     * List/search users. Pick one via ?user= to edit their overrides.
     */
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->q, fn ($q) => $q->where('fullname', 'like', "%{$request->q}%")
                ->orWhere('email', 'like', "%{$request->q}%"))
            ->orderBy('fullname')
            ->paginate(15)
            ->withQueryString();

        $selectedUser = null;
        $menus = collect();
        $overrides = collect();

        if ($request->filled('user')) {
            $selectedUser = User::findOrFail($request->user);
            $menus = Menu::with('children')->topLevel()->orderBy('order')->get();
            $overrides = $selectedUser->menuOverrides()->pluck('access', 'menu_id');
        }

        return view('admin.users.access', compact('users', 'selectedUser', 'menus', 'overrides'));
    }

    /**
     * Save overrides for one user. Incoming payload looks like:
     *   overrides[menu_id] = "allow" | "deny" | "inherit"
     * "inherit" means "no override — fall back to the role permission",
     * so we simply delete the row for that menu.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'overrides'   => ['array'],
            'overrides.*' => ['in:allow,deny,inherit'],
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

        return back()->with('success', "Menu access updated for {$user->name}.");
    }
}
