<?php

namespace App\Models;

use App\Models\Submenu;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'slug',
    'menu_id',
    'category',
    'name',
    'route',
    'icon',
    'is_menu',
    'is_dropdown',
    'order',
])]

class Menu extends Model
{
    protected $table = 'su_menus';

    public function submenus(): HasMany
    {
        return $this->hasMany(Submenu::class, 'x_menu_id', 'menu_id');
    }
}
