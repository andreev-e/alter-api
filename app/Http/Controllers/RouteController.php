<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Http\Resources\RouteResourceCollection;
use App\Models\Route;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RouteController extends Controller
{
    public function index(RouteRequest $request): AnonymousResourceCollection
    {
        $pois = Route::query()
            ->where('show', 1)
            ->orderBy('views', 'DESC');

        $pois->when($request->has('user'), function(Builder $query) use ($request) {
            $query->where('author', $request->get('user'));
        });

        return RouteResourceCollection::collection($pois->paginate(20));
    }
}
