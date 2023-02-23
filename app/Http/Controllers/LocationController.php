<?php

namespace App\Http\Controllers;
use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationResourceCollection;
use App\Http\Resources\TagResourceCollection;
use App\Models\Location;
use App\Http\Resources\TagResource;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return Cache::remember('locations', 60 * 60, function() {
            return LocationResourceCollection::collection(Location::query()
                ->with(['children', 'parent.parent.parent'])
                ->where('parent', 0)
                ->where('count', '>', 0)
                ->orderBy('count', 'DESC')->get());
        });
    }

    public function show(Location $location): LocationResource
    {
        return new LocationResource($location->load(['children', 'parent.parent.parent']));
    }

    public function update(Request $request, Location $location)
    {
        //
    }

    public function destroy(Location $location)
    {
        //
    }
}
