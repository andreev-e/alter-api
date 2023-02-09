<?php

namespace App\Http\Controllers;

use App\Http\Requests\Poi\PoiCreateRequest;
use App\Http\Requests\Poi\PoiRequest;
use App\Http\Requests\Poi\PoiUpdateRequest;
use App\Http\Resources\PoiResource;
use App\Http\Resources\PoiResourceCollection;
use App\Models\Poi;
use App\Models\Route;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PoiController extends Controller
{
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $pois = Poi::query()
            ->where('show', 1)
            ->orderBy('views', 'DESC');

        $pois->when($request->has('tag'), function(Builder $query) use ($request) {
            $query->whereHas('tags', function(Builder $subQuery) use ($request) {
                $subQuery->where('url', $request->get('tag'));
            });
        });

        $pois->when($request->has('location'), function(Builder $query) use ($request) {
            $query->whereHas('locations', function(Builder $subQuery) use ($request) {
                $subQuery->where('url', $request->get('location'));
            });
        });

        $pois->when($request->has('categories'), function(Builder $query) use ($request) {
            $query->whereIn('type', $request->get('categories'));
        });

        $pois->when($request->has('user'), function(Builder $query) use ($request) {
            $query->where('author', $request->get('user'));
        });

        if ($request->route) {
            $route = Route::query()->find($request->route);
            if ($route) {
                $pois->whereIn('id', explode('|', $route->POINTS));
            }
        }

        if ($request->south) {
            $pois->where('lat', '>', $request->south);
        }
        if ($request->north) {
            $pois->where('lat', '<', $request->north);
        }
        if ($request->east) {
            $pois->where('lng', '<', $request->east);
        }
        if ($request->west) {
            $pois->where('lng', '>', $request->west);
        }

        if ($request->south || $request->north || $request->east || $request->west) {
            return PoiResourceCollection::collection($pois->limit(50)->get());
        }

        return PoiResourceCollection::collection($pois->paginate(20));
    }

    public function store(PoiCreateRequest $request): JsonResponse
    {
        if (Auth::user()) {
            Poi::query()->create([
                ...$request->validated(),
                'author' => Auth::user()->username,
                'show' => false,
            ]);
        }
        return response()->json('Ok');
    }

    public function show(Poi $poi): PoiResource
    {
        return new PoiResource($poi->load('locations', 'tags', 'user'));
    }

    public function update(PoiUpdateRequest $request, Poi $poi): JsonResponse
    {
        dump(Auth::user()->username, $poi->author);
        if (Auth::user() && (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev')) {
            $poi->update($request->validated());
        }
        return response()->json('Ok');
    }

    public function destroy(Poi $poi)
    {
        //
    }
}
