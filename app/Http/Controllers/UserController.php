<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Auth;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
}
