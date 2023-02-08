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

Route::get('/api/api/user', [LoginController::class, 'user'])->name('user.login');
Route::post('/api/login', [LoginController::class, 'authenticate'])->name('login');
Route::post('/api/logout', [LoginController::class, 'logout'])->name('logout');


Route::prefix('/api/comment')->controller(CommentController::class)->group(function() {
    Route::post('', 'store')->name('store');
    Route::delete('{comment:commentid}', 'destroy')->name('destroy');
    Route::post('{comment:commentid}/approve', 'approve')->name('approve');
});




