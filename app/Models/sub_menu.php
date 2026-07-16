<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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

class sub_menu extends Model
{
    protected $table = 'su_submenus';
}

