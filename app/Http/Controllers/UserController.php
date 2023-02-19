<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\SetsMediaCustomPropertiesTrait;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\AvatarResource;
use App\Http\Resources\ImageResource;
use App\Http\Resources\UserResource;
use App\Models\Poi;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Intervention\Image\Facades\Image;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Storage;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()->where('publications', '>', 0)
            ->orderBy('publications', 'desc');
        return UserResource::collection($users->paginate());
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
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
                    ->storingConversionsOnDisk('s3')
                    ->preservingOriginal()
                    ->toMediaCollection('user-image', 's3');

                $localPath = Storage::disk('public')
                    ->put($user::TMP_MEDIA_FOLDER, $request->file('image'), 'public');
                $img = Image::make(Storage::disk('public')->get($localPath));

                $media->setCustomProperty('temporary_url', $localPath);

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
}
