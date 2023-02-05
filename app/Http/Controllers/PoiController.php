<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoiRequest;
use App\Http\Resources\PoiResource;
use App\Models\Poi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResourceCollection;
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

    public function store(Request $request)
    {
        //
    }

    public function show(Poi $poi): PoiResource
    {
        return new PoiResource($poi->load('locations', 'tags'));
    }

    public function update(Request $request, Poi $poi)
    {
        //
    }

    public function destroy(Poi $poi)
    {
        //
    }
}
