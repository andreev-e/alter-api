<?php

namespace App\Http\Controllers;

use App\Http\Requests\RouteRequest;
use App\Http\Resources\RouteResource;
use App\Http\Resources\RouteResourceCollection;
use App\Models\Route;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RouteController extends Controller
{
    public function index(RouteRequest $request): AnonymousResourceCollection
    {
        $pois = Route::query()
            ->orderBy('views', 'DESC');

        if (Auth::user()) {
            dd(Auth::user());
            if (Auth::user()->username !== 'andreev') {
                $pois->where(function(Builder $subQuery) {
                    $subQuery->orWhere('author', Auth::user()->username)
                        ->orWhere('show', 1);
                });
            }
        } else {
            $pois->where('show', 1);
        }

        $pois->when($request->has('user'), function(Builder $query) use ($request) {
            $query->where('author', $request->get('user'));
        });

        return RouteResourceCollection::collection($pois->paginate(24));
    }

    public function show(Route $route): RouteResource
    {
        return new RouteResource($route);
    }

    public function approve(Route $route): RouteResource|JsonResponse
    {
        if (Auth::user()->username === 'andreev') {
            $route->show = true;
            $route->save();
            return new RouteResource($route);
        }
        return response()->json('No ok', 405);
    }

    public function disprove(Route $route): RouteResource|JsonResponse
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {
            $route->show = false;
            $route->save();
            return new RouteResource($route);
        }
        return response()->json('Not ok', 405);
    }

    public function destroy(Route $route): JsonResponse
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {
            $route->clearMediaCollection('route-image');
            $route->delete();
            return response()->json('Ok');
        }
        return response()->json('Not ok', 405);
    }

}
