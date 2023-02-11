<?php

namespace App\Http\Controllers;

use App\Http\Requests\Poi\PoiCreateRequest;
use App\Http\Requests\Poi\PoiRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\PoiResource;
use App\Http\Resources\PoiResourceCollection;
use App\Models\Poi;
use App\Models\Route;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class PoiController extends Controller
{
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $pois = Poi::query();

        if (!$request->has('withDisproved')) {
            $pois->where('show', 1);
        }

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

        if ($request->has('latest')) {
            $pois->orderBy('id', 'desc');
        } else {
            $pois->orderBy('views', 'desc');
        }

        if ($request->south || $request->north || $request->east || $request->west) {
            return PoiResourceCollection::collection($pois->limit(50)->get());
        }

        return PoiResourceCollection::collection($pois->paginate(20));
    }

    public function store(PoiCreateRequest $request): JsonResponse|PoiResource
    {
        if (Auth::user()) {
            $poi = Poi::query()->create(
                array_merge(
                    $request->validated(),
                    [
                        'author' => Auth::user()->username,
                        'show' => false,
                    ])
            );
            $poi->tags()->attach($request->get('tags'));
            return new PoiResource($poi->load('locations', 'tags', 'user'));
        }
        return response()->json('No ok', 405);
    }

    public function show(Poi $poi): PoiResource
    {
        return new PoiResource($poi->load('locations', 'tags', 'user'));
    }

    public function update(PoiCreateRequest $request, Poi $poi): JsonResponse
    {
        if (Auth::user() && (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev')) {
            $poi->update($request->validated());
            $poi->tags()->sync($request->get('tags'));
            return response()->json('Ok');
        }
        return response()->json('No ok', 405);
    }

    public function approve(Poi $poi): JsonResponse
    {
        if (Auth::user() && Auth::user()->username === 'andreev') {
            $poi->show = true;
            $poi->save();
            return response()->json('Ok');
        }
        return response()->json('No ok', 405);
    }

    public function disprove(Poi $poi): JsonResponse
    {
        if (Auth::user() &&
            (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev')) {
            $poi->show = false;
            $poi->save();
            return response()->json('Ok');
        }
        return response()->json('No ok', 405);
    }

    public function destroy(Poi $poi): JsonResponse
    {
        if (Auth::user() &&
            (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev')) {
            $poi->tags()->detach();
            $poi->delete();
            return response()->json('Ok');
        }
        return response()->json('No ok', 405);
    }

    public function storeImage(StoreImageRequest $request, Poi $poi): JsonResponse
    {
        if (Auth::user() &&
            (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev')) {

            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $poi->addMediaFromRequest('image')
                    ->storingConversionsOnDisk('s3')
                    ->toMediaCollection('image','s3');
            }

            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }

    public function destroyImage(Poi $poi, Media $media)
    {
        if (Auth::user() &&
            (Auth::user()->username === $media->model->author || Auth::user()->username === 'andreev')) {
            $media->delete();
            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }
}
