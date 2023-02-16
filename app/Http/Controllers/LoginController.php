<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Hash;
use Illuminate\Http\Response;

use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request): Response
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

    public function register(RegisterRequest $request): Response
    {
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'userlevel' => 1,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user, true);
        return response()->noContent();
    }

    public function user(): Response
    {
        return response()->json(Auth::user(), Auth::user() ? 200 : 401);
    }

    public function logout()
    {
        Auth::logout();
    }

}
