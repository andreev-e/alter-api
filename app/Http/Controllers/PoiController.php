<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoiRequest;
use App\Models\Poi;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PoiController extends Controller
{
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $pois = Poi::query()
            ->with(['tags', 'locations'])
            ->where('show', 1)
            ->orderBy('views', 'DESC');

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
            $pois->limit(100);
        }

        $pois->when($request->has('tag'), function(Builder $query) use ($request) {
            $query->whereHas('tags', function($subQuery) use ($request) {
                $subQuery->select('id')->from('tags')
                    ->where('url', $request->get('tag'));
            });
        });

        return PoiResource::collection($pois->limit(100)->get());
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Poi $poi): PoiResource
    {
        return new PoiResource($poi);
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
