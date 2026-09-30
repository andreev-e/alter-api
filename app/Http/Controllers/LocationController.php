<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Http\Resources\LocationResourceCollection;
use App\Http\Resources\TagResourceCollection;
use App\Models\Location;
use App\Http\Resources\TagResource;
use Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LocationController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return Cache::tags([Location::CACHE_TAG, Location::CACHE_TAG_LIST])
            ->rememberForever('locations', function() {
                return LocationResourceCollection::collection(Location::query()
                    ->with(['children', 'parent.parent.parent'])
                    ->where('parent', 0)
                    ->where('count', '>', 3)
                    ->orderBy('count', 'DESC')->get());
            });
    }

    public function show(Location $location): JsonResponse
    {
        // Кэшируем готовый массив: закэшированный ресурс при каждом ответе заново ходит в базу за связями
        $data = Cache::tags([Location::CACHE_TAG, Location::CACHE_TAG . $location->id])
            ->rememberForever('location:v2:' . $location->id, function() use ($location) {
                return (new LocationResource($location->load('children')))->resolve();
            });

        return response()->json(['data' => $data]);
    }
}
