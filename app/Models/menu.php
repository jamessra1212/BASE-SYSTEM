<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class Menu extends Model
{
    protected $fillable = [
        'parent_id', 'name', 'icon', 'route', 'url',
        'permission_name', 'order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        // Auto-generate a unique permission slug + create the matching
        // Spatie permission whenever a menu item is created.
        static::creating(function (Menu $menu) {
            if (empty($menu->permission_name)) {
                $menu->permission_name = static::generateUniquePermissionName($menu->name);
            }
        });

        static::created(function (Menu $menu) {
            Permission::firstOrCreate(['name' => $menu->permission_name, 'guard_name' => 'web']);
        });

        static::deleting(function (Menu $menu) {
            Permission::where('name', $menu->permission_name)->delete();
        });
    }

    public static function generateUniquePermissionName(string $name): string
    {
        $base = 'menu.' . Str::slug($name, '-');
        $slug = $base;
        $i = 1;

        while (static::where('permission_name', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function overrides(): HasMany
    {
        return $this->hasMany(MenuUserOverride::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    public function resolvedUrl(): string
    {
        if ($this->route && \Illuminate\Support\Facades\Route::has($this->route)) {
            return route($this->route);
        }

        return $this->url ?: '#';
    }
}
