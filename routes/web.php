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

Route::get('/api/api/user', [LoginController::class, 'user'])->name('user.login');
Route::post('/api/login', [LoginController::class, 'authenticate'])->name('login');
Route::post('/api/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('/api/comment')->name('comment')->controller(CommentController::class)->group(function() {
    Route::post('', 'store')->name('store');
    Route::patch('{comment:commentid}', 'update')->name('update');
    Route::post('{comment:commentid}/approve', 'approve')->name('approve');
    Route::delete('{comment:commentid}', 'destroy')->name('destroy');
});

Route::prefix('/api/poi')->name('poi')->controller(PoiController::class)->group(function() {
    Route::get('','index')->name('list');
    Route::post('', 'store')->name('store');
    Route::patch('{poi}', 'update')->name('update');
    Route::post('{poi}/approve', 'approve')->name('approve');
    Route::delete('{poi}', 'destroy')->name('destroy');
});




