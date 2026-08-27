<?php

use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserAccessController;
use Illuminate\Support\Facades\Route;

// Nested under the same 'sida' prefix/name/middleware convention as the
// rest of the authenticated routes in web.php, so this file can just be
// require'd at the bottom of web.php as-is.

Route::prefix('sida/admin')
    ->as('sida.admin.')
    ->middleware(['portal.auth'])
    ->group(function () {

        Route::middleware('permission:manage menus')->group(function () {
            Route::get('menus', [MenuController::class, 'index'])->name('menus.index');
            Route::get('menus/data', [MenuController::class, 'data'])->name('menus.data');
            Route::get('menus/entry', [MenuController::class, 'entry'])->name('menus.entry');
            Route::post('menus', [MenuController::class, 'store'])->name('menus.store');
            Route::put('menus/{menu}', [MenuController::class, 'update'])->name('menus.update');
            Route::delete('menus/{menu}', [MenuController::class, 'destroy'])->name('menus.destroy');
        });

        Route::middleware('permission:manage roles')->group(function () {
            Route::get('roles', [RoleController::class, 'index'])->name('roles.index');
            Route::get('roles/data', [RoleController::class, 'data'])->name('roles.data');
            Route::get('roles/entry', [RoleController::class, 'entry'])->name('roles.entry');
            Route::post('roles', [RoleController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])->name('roles.permissions');
            Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update');
            Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        });

        Route::middleware('permission:manage users')->group(function () {
            Route::get('users/access', [UserAccessController::class, 'index'])->name('users.access');
            Route::put('users/{user}/access', [UserAccessController::class, 'update'])->name('users.access.update');
        });
    });
