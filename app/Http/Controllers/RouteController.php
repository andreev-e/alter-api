<?php

namespace App\Http\Controllers;

use App\Http\Requests\Route\RouteCreateRequest;
use App\Http\Requests\Route\RouteRequest;
use App\Http\Requests\StoreImageRequest;
use App\Http\Resources\RouteResource;
use App\Http\Resources\RouteResourceCollection;
use App\Models\Route;
use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Storage;

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

    public function update(RouteCreateRequest $request, Route $route): RouteResource|JsonResponse
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {
            $route->update($request->validated());
            return new RouteResource($route->load($route->defaultRelations));
        }
        return response()->json('No ok', 405);
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

    public function storeImage(StoreImageRequest $request, Route $route): JsonResponse
    {
        if (Auth::user()->username === $route->author || Auth::user()->username === 'andreev') {

            if ($request->file('image')) {
                $media = $route->addMediaFromRequest('image')
                    ->storingConversionsOnDisk('s3')
                    ->preservingOriginal()
                    ->toMediaCollection('route-image', 's3');

                $localPath = Storage::disk('public')
                    ->put($route::TMP_MEDIA_FOLDER, $request->file('image'), 'public');
                $img = Image::make(Storage::disk('public')->get($localPath));

                $this->setMediaCustomProperties($media, $localPath, $img);

                $img->widen($route::THUMB_SIZE)
                    ->crop($route::THUMB_SIZE, $route::THUMB_SIZE)
                    ->save(storage_path('/app/public/') . $localPath);
            }

            return response()->json(RouteResource::collection($route->media));
        }
        return response()->json('No ok', 405);
    }
}
