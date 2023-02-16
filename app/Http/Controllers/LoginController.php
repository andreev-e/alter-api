<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): \Illuminate\Http\Response
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return response()->noContent();
        }

        $user = User::query()
            ->where('email', '=', $credentials['email'])
            ->first();

        if (isset($user)) {
            if ($user->password == md5($credentials['password'])) {
//                $user->password = Hash::make(Input::get('password'));
//                $user->save();
                Auth::login($user, true);
                return response()->noContent();
            }
        }

        return response()->noContent(401);
    }

    public function register(RegisterRequest $request)
    {
        dd($request);
    }

    public function user()
    {
        return response()->json(Auth::user(), Auth::user() ? 200 : 401);
    }

    public function logout()
    {
        Auth::logout();
    }

}
