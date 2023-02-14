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
use Illuminate\Http\Response;

class RouteController extends Controller
{
    public function index(RouteRequest $request): AnonymousResourceCollection
    {
        $routes = Route::query()
            ->orderBy('views', 'DESC');

        if (Auth::user()) {
            if (Auth::user()->username !== 'andreev') {
                $routes->where(function(Builder $subQuery) {
                    $subQuery->orWhere('author', Auth::user()->username)
                        ->orWhere('show', 1);
                });
            }
        } else {
            $routes->where('show', 1);
        }

        $routes->when($request->has('user'), function(Builder $query) use ($request) {
            $query->where('author', $request->get('user'));
        });

        return RouteResourceCollection::collection($routes->paginate(24));
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
        }
        return new RouteResource($route);
    }

    public function disprove(Route $route): RouteResource
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {
            $route->show = false;
            $route->save();
        }
        return new RouteResource($route);
    }

    public function destroy(Route $route): Response
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {
            $route->clearMediaCollection('route-image');
            $route->delete();
        }
        return response()->noContent();
    }

}
