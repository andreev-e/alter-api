<?php

namespace App\Http\Controllers;

use App\Http\Requests\PoiRequest;
use App\Models\Poi;
use Illuminate\Http\Request;
use App\Http\Resources\PoiResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PoiController extends Controller
{
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $pois = Poi::query()
            ->where('show', 1)->orderBy('views', 'DESC');

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
            $pois->with('tags')->limit(100);
        }

        $pois->when($request->get('tag'), function($query) use ($request) {
            $query->has('tags', function($query) use ($request) {
                $query->where('slug', $request->get('tag'));
            });
        });

        // dump($pois->limit(10)->get()->toArray());
        // return;

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
