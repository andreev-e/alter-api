<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SetsMediaCustomPropertiesTrait;
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
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class PoiController extends Controller
{
    use SetsMediaCustomPropertiesTrait;

    public function index(PoiRequest $request): AnonymousResourceCollection
    {
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
        } else {
            $pois->orderBy('views', 'desc');
        }

        if ($request->south || $request->north || $request->east || $request->west) {
            return PoiResourceCollection::collection($pois->limit(50)->get());
        }

        return PoiResourceCollection::collection($pois->paginate(24));
    }

    public function store(PoiCreateRequest $request): JsonResponse|PoiResource
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
        return new PoiResource($poi->load('locations', 'tags', 'user'));
    }

    public function show(Poi $poi): PoiResource
    {
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
            return new PoiResource($poi->load($poi->defaultRelations));
        }
        return response()->json('No ok', 405);
    }

    public function approve(Poi $poi): PoiResource|JsonResponse
    {
        if (Auth::user()->username === 'andreev') {
            $poi->show = true;
            $poi->save();
            return new PoiResource($poi->load($poi->defaultRelations));
        }
        return response()->json('No ok', 405);
    }

    public function disprove(Poi $poi): PoiResource|JsonResponse
    {
        if (Auth::user()->username === $poi->author || Auth::user()->username === 'andreev') {
            $poi->show = false;
            $poi->save();
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
                    ->storingConversionsOnDisk('s3')
                    ->preservingOriginal()
                    ->toMediaCollection('poi-image', 's3');

                $localPath = Storage::disk('public')
                    ->put($poi::TMP_MEDIA_FOLDER, $request->file('image'), 'public');
                $img = Image::make(Storage::disk('public')->get($localPath));

                $this->setMediaCustomProperties($media, $localPath, $img);

                $img->widen($poi::THUMB_SIZE)
                    ->crop($poi::THUMB_SIZE, $poi::THUMB_SIZE)
                    ->save(storage_path('/app/public/') . $localPath);
            }

            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }

    public function destroyImage(Poi $poi, Media $media)
    {
        if (Auth::user()->username === $media->model->author || Auth::user()->username === 'andreev') {
            $media->delete();
            return response()->json(ImageResource::collection($poi->media));
        }
        return response()->json('No ok', 405);
    }
}
