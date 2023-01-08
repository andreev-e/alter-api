<?php

use App\Http\Controllers\PoiController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\TagController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('tag', [TagController::class, 'index'])->name('tags');
Route::get('locations', [TagController::class, 'locations'])->name('locations');
Route::get('poi', [PoiController::class, 'index'])->name('pois');

Route::get('{type}', [ResourceController::class, 'index'])->name('resource.index');
