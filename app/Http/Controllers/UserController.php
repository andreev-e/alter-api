<?php

namespace App\Http\Controllers;

use App\Http\Requests\Image\SortImageRequest;
use App\Http\Requests\Image\StoreImageRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\AvatarResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Cache;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;
use Illuminate\Http\Request;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class UserController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return Cache::remember('users:p' . $request->page, 60 * 60 * 24, function() {
            $users = User::query()
                ->whereHas('media')
                ->where('publications', '>', 0)
                ->orderBy('publications', 'desc');
            return UserResource::collection($users->paginate(30));
        });
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load(['checkins.poi', 'media']));
    }

    public function update(UpdateRequest $request, User $user): UserResource|JsonResponse
    {
        if (Auth::user()->username === $user->username || Auth::user()->username === 'andreev') {
            $user->update($request->except('password'));

            if ($request->get('password')) {
                $user->password = Hash::make($request->get('password'));
                $user->save();
            }
            return new UserResource($user->fresh());
        }
        return response()->json('No ok', 405);
    }

    /**
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     */
    public function storeImage(StoreImageRequest $request, User $user): JsonResponse
    {
        if (Auth::user()->username === $user->username || Auth::user()->username === 'andreev') {

            if ($request->file('image')) {
                $media = $user->addMediaFromRequest('image')
                    ->storingConversionsOnDisk('public')
                    ->preservingOriginal()
                    ->toMediaCollection('user-image', 'public');

                $localPath = Storage::disk('public')
                    ->put($user::TMP_MEDIA_FOLDER, $request->file('image'), 'public');
                $img = Image::make(Storage::disk('public')->get($localPath));

                $media->setCustomProperty('temporary_url', $localPath);
                $media->save();

                $img->widen($user::THUMB_SIZE)
                    ->crop($user::THUMB_SIZE, $user::THUMB_SIZE)
                    ->save(storage_path('/app/public/') . $localPath);
            }

            return response()->json(AvatarResource::collection($user->media));
        }
        return response()->json('No ok', 405);
    }

    public function destroyImage(User $user, Media $media)
    {
        if (Auth::user()->username === $media->model->username || Auth::user()->username === 'andreev') {
            $media->delete();
            return response()->json(AvatarResource::collection($user->media));
        }
        return response()->json('No ok', 405);
    }

    public function sortImages(SortImageRequest $request, User $user): JsonResponse|Response
    {
        if (Auth::user()->username === $user->author || Auth::user()->username === 'andreev') {
            $user->sortImages($request->get('order'));
            return response()->json(AvatarResource::collection($user->fresh()->media));
        }
        return response()->noContent(405);
    }
}
