<?php

namespace App\Core\Http\Controllers;

use App\Core\DataTables\MenusDataTable;
use App\Http\Controllers\Controller;
use App\Core\Models\Menu;
use Illuminate\Http\Request;

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

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log("created menu \"{$menu->name}\"");

        return response()->json(['status' => 'success', 'message' => 'Menu item created.']);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validated($request, $menu->id);
        $menu->update($data);

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log("updated menu \"{$menu->name}\"");

        return response()->json(['status' => 'success', 'message' => 'Menu item updated.']);
    }

    public function destroy(Menu $menu)
    {
        $name = $menu->name;

        activity()
            ->causedBy(auth()->user())
            ->performedOn($menu)
            ->log("deleted menu \"{$name}\"");

        $menu->delete(); // children auto-detach via nullOnDelete on parent_id

        return response()->json(['status' => 'success', 'message' => 'Menu item deleted.']);
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