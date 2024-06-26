<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SetsMediaCustomPropertiesTrait;
use App\Http\Requests\Image\SortImageRequest;
use App\Http\Requests\Image\StoreImageRequest;
use App\Http\Requests\Poi\PoiCreateRequest;
use App\Http\Requests\Poi\PoiRequest;
use App\Http\Resources\ImageResource;
use App\Http\Resources\PoiResource;
use App\Http\Resources\PoiResourceCollection;
use App\Models\Poi;
use App\Models\Route;
use Auth;
use Cache;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class PoiController extends Controller
{
    use SetsMediaCustomPropertiesTrait;

    private const POI_LIST_CACHE_KEY = 'poi_list';

    /**
     * @throws \JsonException
     */
    public function index(PoiRequest $request): AnonymousResourceCollection
    {
        $key = md5(json_encode($request->all(), JSON_THROW_ON_ERROR));
        return Cache::tags(self::POI_LIST_CACHE_KEY)->remember($key, 60 * 60 * 24, function() use ($request) {
            $pois = Poi::query();

            if ($request->onlyHidden) {
                $pois->where(function(Builder $query) {
                    return $query->orWhere('show', 0)
                        ->orWhere('lat', 0)
                        ->orWhere('lng', 0);
                });
            } else {
                if (!$request->withHidden) {
                    $pois->where('show', 1)
                        ->where('lat', '<>', 0)
                        ->where('lng', '<>', 0);
                }
            }

            if ($request->route) {
                $route = Route::query()->find($request->route);
                if ($route) {
                    return PoiResourceCollection::collection($route->pois());
                }
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

            $pois->when($request->has('list'), function(Builder $query) use ($request) {
                $query->whereIn('id', $request->get('list'));
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

            if ($request->has('latest')) {
                $pois->orderBy('id', 'desc');
            }

            if ($request->has('popular')) {
                $pois->orderBy('views', 'desc');
            }

            if ($request->has('updated')) {
                $pois->where('created_at', '<', Carbon::now()->subMonth())
                    ->orderBy('updated_at', 'desc');
            }

            if ($request->south || $request->north || $request->east || $request->west) {
                $pois = $pois->limit(150)
                    ->orderBy('views', 'desc')
                    ->get();
                return PoiResourceCollection::collection($pois);
            }

            if ($request->has('keyword')) {
                $pois->where('name', 'LIKE', '%' . $request->keyword . '%');

                if ($request->has('near')) {
                    [$lat, $lng] = explode(';', $request->near);

                    $pois->select(DB::raw("*,
                111.111 * DEGREES(ACOS(LEAST(1.0, COS(RADIANS(lat))
                     * COS(RADIANS($lat))
                     * COS(RADIANS(lng - $lng))
                     + SIN(RADIANS(lat))
                     * SIN(RADIANS($lat))))) AS 'dist'"))
                        ->where('lat', '<>', 0)
                        ->where('lng', '<>', 0)
                        ->havingRaw('dist IS NOT NULL')
                        ->orderBy('dist');
                }

                return PoiResourceCollection::collection($pois->limit(10)->get());
            }

            return PoiResourceCollection::collection($pois->paginate(24));
        });
    }

    public function store(PoiCreateRequest $request): PoiResource
    {
        $poi = Poi::query()->create(
            array_merge(
                $request->validated(),
                [
                    'author' => Auth::user()->username,
                    'show' => false,
                ])
        );
        $poi->tags()->sync($request->get('tags'));

        Cache::tags(self::POI_LIST_CACHE_KEY)->flush();

        return new PoiResource($poi->load('locations', 'tags', 'user'));
    }

    public function show(Poi $poi): PoiResource
    {
        $poi->timestamps = false;
        $poi->update([
            'views' => $poi->views + 1,
            'views_month' => $poi->views_month + 1,
            'views_today' => $poi->views_today + 1,
        ]);
        return new PoiResource($poi->load($poi->defaultRelations));
    }

    public function update(PoiCreateRequest $request, Poi $poi): PoiResource|JsonResponse
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {
            $poi->update($request->validated());
            $poi->tags()->sync($request->get('tags'));

            Cache::tags(self::POI_LIST_CACHE_KEY)->flush();

            return new PoiResource($poi->load($poi->defaultRelations));
        }

        return response()->json('No ok', 405);
    }

    public function approve(Poi $poi): PoiResource|JsonResponse
    {
        if (Auth::user()->username === 'andreev') {
            $poi->show = true;
            $poi->save();

            Cache::tags(self::POI_LIST_CACHE_KEY)->flush();

            return new PoiResource($poi->load($poi->defaultRelations));
        }
        return response()->json('No ok', 405);
    }

    public function disprove(Poi $poi): PoiResource|JsonResponse
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {
            $poi->show = false;
            $poi->save();

            Cache::tags(self::POI_LIST_CACHE_KEY)->flush();

            return new PoiResource($poi->load('locations', 'tags', 'user'));
        }
        return response()->json('Not ok', 405);
    }

    public function destroy(Poi $poi): JsonResponse
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {
            $poi->twits()->delete();
            $poi->tags()->detach();
            $poi->clearMediaCollection('poi-image');
            $poi->delete();

            Cache::tags(self::POI_LIST_CACHE_KEY)->flush();

            return response()->json('Ok');
        }
        return response()->json('Not ok', 405);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function storeImage(StoreImageRequest $request, Poi $poi): JsonResponse
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {

            if ($request->file('image')) {
                $media = $poi->addMediaFromRequest('image')
                    ->storingConversionsOnDisk('public')
                    ->preservingOriginal()
                    ->toMediaCollection('poi-image', 'public');

                $localPath = Storage::disk('public')
                    ->put($poi::TMP_MEDIA_FOLDER, $request->file('image'), 'public');
                $img = Image::make(Storage::disk('public')->get($localPath));

                $this->setMediaCustomProperties($media, $localPath, $img);

                $img->widen($poi::THUMB_SIZE)
                    ->crop($poi::THUMB_SIZE, $poi::THUMB_SIZE)
                    ->save(storage_path('/app/public/') . $localPath);
                $poi->dominatecolor = null;
                $poi->save();
            }

            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }

    public function destroyImage(Poi $poi, Media $media): JsonResponse|Response
    {
        if (Auth::user()->username === $media->model->author || Auth::user()->username === 'andreev') {
            $media->delete();
            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }

    public function sortImages(SortImageRequest $request, Poi $poi): JsonResponse|Response
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {
            $poi->sortImages($request->get('order'));
            return response()->json(ImageResource::collection($poi->fresh()->media));
        }
        return response()->noContent(405);
    }

    public function toggleFavorite(Poi $poi): JsonResponse
    {
        $user = Auth::user();
        if ($user) {
            $favorites = $user->favorites ?? [];

            if (!in_array($poi->id, $favorites)) {
                $favorites[] = $poi->id;
            } else {
                if (($key = array_search($poi->id, $favorites)) !== false) {
                    unset($favorites[$key]);
                }
            }

            $user->favorites = array_values($favorites);
            $user->save();
        }
        return response()->json($favorites);
    }
}
