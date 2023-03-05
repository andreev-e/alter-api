<?php

use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PoiController;
use App\Http\Controllers\QRController;
use App\Http\Controllers\RouteController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [LoginController::class, 'login'])->name('login');
Route::post('register', [LoginController::class, 'register'])->name('register');

Route::get('user', [UserController::class, 'index'])->name('user');

Route::prefix('tag')->name('tag')->group(function() {
    Route::get('', [TagController::class, 'index'])->name('list');
    Route::get('/{tag:url}', [TagController::class, 'show'])->name('slug');
});

Route::prefix('location')->name('location')->group(function() {
    Route::get('', [LocationController::class, 'index'])->name('list');
    Route::get('/{location:url}', [LocationController::class, 'show'])->name('slug');
});

Route::prefix('user')->name('user')->group(function() {
    Route::get('/{user:username}', [UserController::class, 'show'])->name('username');
});

Route::get('comment', [CommentController::class,'index'])->name('comment.list');

Route::prefix('poi')->name('poi')
    ->controller(PoiController::class)
    ->group(function() {
        Route::get('{poi}', 'show')->name('show');
        Route::get('', 'index')->name('list');
    });

Route::prefix('route')->name('route')
    ->controller(RouteController::class)
    ->group(function() {
        Route::get('', 'index')->name('list');
        Route::get('{route}', 'show')->name('show');
    });

// AUTHORIZED
Route::middleware('auth')->group(function() {

    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/api/user', [LoginController::class, 'user'])
        ->name('user.login');

    Route::prefix('user')->name('user')
        ->controller(UserController::class)
        ->group(function() {
            Route::patch('{user:username}', 'update')->name('update');
            Route::post('{user:username}/image', 'storeImage')->name('image.store');
            Route::delete('{user:username}/image/{media}', 'destroyImage')->name('image.destroy');
            Route::post('{user:username}/sort-images', 'sortImages')->name('sort-images');
        });

    Route::prefix('poi')->name('poi')
        ->controller(PoiController::class)
        ->group(function() {
            Route::post('', 'store')->name('store');
            Route::patch('{poi}', 'update')->name('update');
            Route::patch('{poi}/toggle-favorite', 'toggleFavorite')->name('toggle-favorite');
            Route::post('{poi}/approve', 'approve')->name('approve');
            Route::post('{poi}/disprove', 'disprove')->name('disprove');
            Route::post('{poi}/sort-images', 'sortImages')->name('sort-images');
            Route::delete('{poi}', 'destroy')->name('destroy');
            Route::post('{poi}/image', 'storeImage')->name('image.store');
            Route::delete('{poi}/image/{media}', 'destroyImage')->name('image.destroy');
        });

    Route::prefix('route')->name('route')
        ->controller(RouteController::class)
        ->group(function() {
            Route::post('', 'store')->name('store');
            Route::patch('{route}', 'update')->name('update');
            Route::post('{route}/approve', 'approve')->name('approve');
            Route::post('{route}/disprove', 'disprove')->name('disprove');
            Route::post('{route}/sort-images', 'sortImages')->name('sort-images');
            Route::delete('{route}', 'destroy')->name('destroy');
            Route::post('{route}/image', 'storeImage')->name('image.store');
            Route::delete('{route}/image/{media}', 'destroyImage')->name('image.destroy');
        });

    Route::prefix('comment')->name('comment')
        ->controller(CommentController::class)
        ->group(function() {
            Route::post('', 'store')->name('store');
            Route::patch('{comment:commentid}', 'update')->name('update');
            Route::post('{comment:commentid}/approve', 'approve')->name('approve');
            Route::delete('{comment:commentid}', 'destroy')->name('destroy');
        });

    Route::prefix('checkin')->name('checkin')
        ->controller(CheckinController::class)
        ->group(function() {
            Route::patch('{poi}', 'toggle')->name('toggle');
        });

});



