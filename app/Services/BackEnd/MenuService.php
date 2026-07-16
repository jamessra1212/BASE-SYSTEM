<?php

namespace App\Services\BackEnd;


use App\Models\menu;
use App\Models\sub_menu;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuService
{
    /**
     * Create a new class instance.
     */
    public function __construct(protected menu $menu)
    {

    }

    public function create(array $data): menu
    {
        try {

            return DB::transaction(function () use ($data) {

                $trimmedRoute = Str::of($data['route'])->rtrim('.');

                $menu = menu::create([
                    'slug'          => Str::random(15),
                    'menu_id'       => strtoupper(Str::random(6)),
                    'name'          => $data['name'],
                    'route'         => $trimmedRoute,
                    'category'      => $data['category'],
                    'icon'          => $data['icon'] ?? null,
                    'is_menu'       => isset($data['is_menu']),
                    'is_dropdown'   => isset($data['is_dropdown']),
                ]);

                $submenus = [];

                foreach ($data['submenus'] ?? [] as $submenu) {
                    $submenus[] = [
                        'slug'       => Str::random(15),
                        'sub_menu_id' => strtoupper(Str::random(6)),
                        'x_menu_id'    => $menu->menu_id,
                        'name'       => $menu->name . ' ' . ucfirst($submenu),
                        'route'      => $trimmedRoute . '.' . $submenu,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($submenus)) {
                    sub_menu::insert($submenus);
                }

                return $menu;
            });
        } catch (\Exception $e) {
            // Log the exception or handle it as needed
            throw new \RuntimeException('Failed to create menu: ' . $e->getMessage(), 0, $e);
        }


    }
}
