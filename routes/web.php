<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\CommentController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PoiController;
use App\Http\Controllers\RouteController;

Route::get('/api/api/user', [LoginController::class, 'user'])->name('user.login');
Route::post('/api/login', [LoginController::class, 'authenticate'])->name('login');
Route::post('/api/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('/api/comment')->name('comment')->controller(CommentController::class)
    ->group(function() {
        Route::post('', 'store')->name('store');
        Route::patch('{comment:commentid}', 'update')->name('update');
        Route::post('{comment:commentid}/approve', 'approve')->name('approve');
        Route::delete('{comment:commentid}', 'destroy')->name('destroy');
    });

Route::prefix('/api/poi')->name('poi')->controller(PoiController::class)
    ->group(function() {
        Route::get('{poi}','show')->name('show');
        Route::get('', 'index')->name('list');
        Route::post('', 'store')->name('store');
        Route::patch('{poi}', 'update')->name('update');
        Route::post('{poi}/approve', 'approve')->name('approve');
        Route::post('{poi}/disprove', 'disprove')->name('disprove');
        Route::delete('{poi}', 'destroy')->name('destroy');
        Route::post('{poi}/image', 'storeImage')->name('image.store');
        Route::delete('{poi}/image/{media}', 'destroyImage')->name('image.destroy');
    });


Route::prefix('/api/route')->name('route')->controller(RouteController::class)
    ->group(function() {
        Route::get('', 'index')->middleware('auth:sanctum')->name('list');
        Route::get('{route}','show')->name('show');
        Route::post('', 'store')->name('store');
        Route::patch('{route}', 'update')->name('update');
        Route::post('{route}/approve', 'approve')->name('approve');
        Route::post('{route}/disprove', 'disprove')->name('disprove');
        Route::delete('{route}', 'destroy')->name('destroy');
        Route::post('{route}/image', 'storeImage')->name('image.store');
        Route::delete('{route}/image/{media}', 'destroyImage')->name('image.destroy');
    });




