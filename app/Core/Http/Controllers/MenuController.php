<?php

namespace App\Core\Http\Controllers;

use App\Core\DataTables\MenusDataTable;
use App\Core\Models\Menu;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class MenuController extends Controller
{
    public function index(MenusDataTable $dataTable)
    {
        return $dataTable->render('admin.menus.index');
    }

    public function entry(Request $request)
    {
        $menu = $request->filled('id') ? Menu::findOrFail($request->id) : new Menu();
        $parents = Menu::whereNull('parent_id')
            ->when($menu->exists, fn ($q) => $q->where('id', '!=', $menu->id))
            ->orderBy('order')
            ->get();

        return view('admin.menus._form', compact('menu', 'parents'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $menu = Menu::create($data);

        activity()->causedBy(auth()->user())->performedOn($menu)->log("created menu \"{$menu->name}\"");

        $this->createQuickActions($request, $menu);

        return response()->json(['status' => 'success', 'message' => 'Menu item created.']);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validated($request, $menu->id);
        $menu->update($data);

        activity()->causedBy(auth()->user())->performedOn($menu)->log("updated menu \"{$menu->name}\"");

        return response()->json(['status' => 'success', 'message' => 'Menu item updated.']);
    }

    public function destroy(Menu $menu)
    {
        $name = $menu->name;

        activity()->causedBy(auth()->user())->performedOn($menu)->log("deleted menu \"{$name}\"");

        $menu->delete(); // children auto-detach via nullOnDelete on parent_id

        return response()->json(['status' => 'success', 'message' => 'Menu item deleted.']);
    }

    /**
     * For each checked "quick action": either a nav-visible child Menu
     * (gets its own route to fill in later, and its own auto-generated
     * permission — same as any normal menu) or a plain linked Permission
     * on the parent (no nav appearance, just a permission gate).
     */
    protected function createQuickActions(Request $request, Menu $menu): void
    {
        $includedActions = collect($request->input('quick_actions', []));
        $navActions = collect($request->input('quick_actions_nav', []));

        if ($includedActions->isEmpty()) {
            return;
        }

        $baseSlug = Str::slug($menu->name);
        $order = 1;
        $createdCount = 0;

        foreach ($includedActions as $action) {
            $isNav = $navActions->contains($action);

            if ($isNav) {
                Menu::create([
                    'parent_id' => $menu->id,
                    'name' => "{$menu->name} {$action}",
                    'order' => $order++,
                    'is_active' => true,
                ]);
            } else {
                // firstOrCreate guards against a name collision if this
                // exact action was somehow already created for this menu.
                Permission::firstOrCreate(
                    ['name' => "{$baseSlug}.".strtolower($action)],
                    ['group' => $menu->name, 'menu_id' => $menu->id, 'guard_name' => 'web']
                );
            }

            $createdCount++;
        }

        if ($createdCount > 0) {
            activity()->causedBy(auth()->user())->performedOn($menu)
                ->log("auto-created {$createdCount} quick action(s) for menu \"{$menu->name}\"");
        }
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable', 'exists:menus,id',
                $ignoreId ? \Illuminate\Validation\Rule::notIn([$ignoreId]) : 'nullable',
            ],
            'icon'      => ['nullable', 'string', 'max:100'],
            'route'     => ['nullable', 'string', 'max:255'],
            'url'       => ['nullable', 'string', 'max:255'],
            'order'     => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);
    }
}