<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PoiController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/profile', function(Request $request) {
    return $request->user();
});


Route::get('countries', [TagController::class, 'countries'])->name('countries');
Route::get('user', [UserController::class, 'index'])->name('user');

Route::prefix('tag')->name('tag')->group(function() {
    Route::get('', [TagController::class, 'index'])->name('list');
    Route::get('/{tag:url}', [TagController::class, 'show'])->name('slug');
});

Route::prefix('user')->name('tag')->group(function() {
    Route::get('/{user:username}', [UserController::class, 'show'])->name('username');
});

Route::prefix('comment')
    ->name('comment')
    ->controller(CommentController::class)
    ->group(function() {
        Route::get('','index')->name('list');
    });

Route::prefix('poi')
    ->name('poi')
    ->controller(PoiController::class)
    ->group(function() {
        Route::get('','index')->name('list');
        Route::get('{poi}','show')->name('show');
    });

Route::get('{type}', [ResourceController::class, 'index'])->name('resource.index');
