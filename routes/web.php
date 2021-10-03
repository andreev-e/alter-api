<?php

use Illuminate\Support\Facades\Route;
use App\Http\Resources\TagResource;
use App\Http\Resources\PoiResource;
use App\Models\Tag;
use App\Models\Poi;
use App\Http\Controllers\TagController;

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

Route::resource('tag', TagController::class);

Route::get('/tags', function () {
    return TagResource::collection(Tag::where('TYPE','=',0)->take(25)->orderBy('COUNT', 'DESC')->get());
});
Route::get('/countries', function () {
    return TagResource::collection(Tag::where('TYPE','=',1)->take(25)->orderBy('COUNT', 'DESC')->get());
});
// Route::get('/pois', function () {
//     return PoiResource::collection(Poi::where('show', '=', 1)->where('lat', '>', 0)->where('lng', '>', 0)->take(10)->orderBy('date', 'DESC')->get());
// });

Route::apiResource('/pois', 'App\Http\Controllers\PoiController');
