<?php

namespace App\Models;

use App\Models\Menu;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'slug',
    'submenu_id',
    'x_menu_id',
    'name',
    'nav_name',
    'route',
    'is_nav',
    'sort',
    'public',
])]

class Submenu extends Model
{
    protected $table = 'su_submenus';

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'x_menu_id','menu_id');
    }
}

