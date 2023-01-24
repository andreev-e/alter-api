<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $users = User::query()->where('publications', '>', 0)
            ->orderBy('publications','desc');
        return UserResource::collection($users->paginate());
    }

}
