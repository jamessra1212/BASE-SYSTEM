<?php

use App\Http\Controllers\BackEnd\LoginController;
use App\Http\Controllers\BackEnd\MainController;
use App\Http\Controllers\BackEnd\UserController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.sida.login');
});


Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
    // Add ->middleware('guest') to these two routes
    Route::get('sida/login', [LoginController::class, 'showLoginForm'])
        ->middleware('portal.guest')
        ->name('sida.login');

    Route::post('sida/login', [LoginController::class, 'login'])
        ->middleware('portal.guest')
        ->name('sida.attempt');

    Route::post('sida/google', [LoginController::class, 'redirectToGoogle'])
        ->name('sida.redirect');

    Route::get('sida/google/callback', [LoginController::class, 'handleGoogleCallback'])
        ->name('sida.callback');

    // Do NOT add guest middleware to logout, otherwise logged-in users can't log out!
    Route::post('sida/logout', [LoginController::class, 'logout'])
        ->name('sida.logout');
});

Route::prefix('sida')
    ->as('sida.')
    ->middleware(['portal.auth']) // 'web' is already applied automatically in web.php
    ->group(function () {

    Route::get('main/home', [MainController::class, 'main_home'])->name('main.home');
    Route::match(['get','post'],'main/profile', [MainController::class, 'main_profile'])->name('main.profile');

    Route::get('main/user', [UserController::class, 'main_user'])->name('main.user');
    Route::get('main/user/entry', [UserController::class, 'user_entry'])->name('user.entry');
    Route::post('main/user/store', [UserController::class, 'user_store'])->name('user.store');

    Route::get('main/user/cpass', [UserController::class, 'user_cpass'])->name('user.cpass');
    Route::post('main/user/upass', [UserController::class, 'user_upass'])->name('user.upass');
    Route::post('main/user/ustat', [UserController::class, 'user_ustat'])->name('user.ustat');
    Route::delete('main/user/destroy', [UserController::class, 'user_destroy'])->name('user.destroy');

    Route::resource('main/menu', MenuController::class);
    Route::get('main/menu/{slug}/submenus', [MenuController::class, 'submenus'])->name('menu.submenus');

});
