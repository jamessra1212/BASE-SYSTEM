<?php

namespace App\Services\BackEnd;



use App\Models\Menu;
use App\Models\Submenu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected Menu $menu, Submenu $submenu)
    {

    }

    public function findbySlug($slug){
        return $this->menu->query()->where('menu_id',$slug)->firstOrFail();
    }

    public function create(array $data): Menu
    {
        try {
            return DB::transaction(function () use ($data) {
                $trimmedRoute = Str::of($data['route'])->rtrim('.');

                $menu = Menu::create([
                    'slug'        => Str::random(15),
                    'menu_id'     => strtoupper(Str::random(6)),
                    'name'        => $data['name'],
                    'route'       => (string) $trimmedRoute,
                    'category'    => $data['category'],
                    'icon'        => $data['icon'],
                    'order'       => $data['order'] ?? 0,
                    'is_menu'     => $data['is_menu'] ?? false,
                    'is_dropdown' => $data['is_dropdown'] ?? false,
                ]);

                $submenus = [];
                foreach ($data['submenus'] ?? [] as $submenu) {
                    $submenus[] = [
                        'slug'        => Str::random(15),
                        'sub_menu_id' => strtoupper(Str::random(6)),
                        'x_menu_id'   => $menu->menu_id,
                        'name'        => $menu->name . ' ' . ucfirst($submenu),
                        'route'       => $trimmedRoute . '.' . $submenu,
                        'sort'        => 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }

                if (!empty($submenus)) {
                    Submenu::insert($submenus);
                }

                return $menu;
            });
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to create menu: ' . $e->getMessage(), 0, $e);
        }
    }


}
