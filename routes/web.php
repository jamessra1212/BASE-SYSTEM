<?php

use App\Http\Controllers\BackEnd\LoginController;
use App\Http\Controllers\BackEnd\MainController;
use App\Http\Controllers\BackEnd\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('auth.login');
});


Route::group(['prefix' => 'auth', 'as' => 'auth.'], function() {
    // Add ->middleware('guest') to these two routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])
        ->middleware('portal.guest')
        ->name('login');

    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('portal.guest')
        ->name('attempt');

    Route::post('/google', [LoginController::class, 'redirectToGoogle'])
        ->name('redirect');

    Route::get('/google/callback', [LoginController::class, 'handleGoogleCallback'])
        ->name('callback');

    // Do NOT add guest middleware to logout, otherwise logged-in users can't log out!
    Route::post('/logout', [LoginController::class, 'logout'])
        ->name('logout');
});

Route::prefix('sida')
    ->as('sida.')
    ->middleware(['portal.auth']) // 'web' is already applied automatically in web.php
    ->group(function () {

    Route::get('main/home', [MainController::class, 'main_home'])->name('main.home');
    Route::match(['get','post'],'main/profile', [MainController::class, 'main_profile'])->name('main.profile');

    Route::get('main/user', [UserController::class, 'main_user'])->name('main.user');
    Route::get('main/user/entry', [UserController::class, 'user_entry'])->name('user.entry');
    // Route::post('main/user/store', [UserController::class, 'user_store'])->name('user.store');
    Route::post('main/user/store', [UserController::class, 'user_store'])->name('user.store')->middleware('perm:user.store');
Route::delete('main/user/destroy', [UserController::class, 'user_destroy'])->name('user.destroy')->middleware('perm:user.destroy');

    Route::get('main/user/cpass', [UserController::class, 'user_cpass'])->name('user.cpass');
    Route::post('main/user/upass', [UserController::class, 'user_upass'])->name('user.upass');
    Route::post('main/user/ustat', [UserController::class, 'user_ustat'])->name('user.ustat');
    // Route::delete('main/user/destroy', [UserController::class, 'user_destroy'])->name('user.destroy');
});


require __DIR__.'/admin.php';
