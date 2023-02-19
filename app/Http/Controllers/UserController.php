<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()->where('publications', '>', 0)
            ->orderBy('publications','desc');
        return UserResource::collection($users->paginate());
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user);
    }

    public  function update(UpdateRequest $request, User $user): UserResource
    {
        $user->update($request->except('password'));

        if ($request->has('password')) {
            $user->password = Hash::make($request->get('password'));
            $user->save();
        }

        return new UserResource($user->fresh());
    }

}
