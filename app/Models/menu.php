<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'slug',
    'menu_id',
    'category',
    'name',
    'route',
    'icon',
    'is_menu',
    'is_drpdwn',
    'order',
])]

class menu extends Model
{
    protected $table = 'su_menus';
}
