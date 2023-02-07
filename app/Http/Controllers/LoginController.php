<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function authenticate(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            return response()->json(Auth::getUser());
        }

        $user = User::query()->where('email', '=', $credentials['email'])->first();

        if (isset($user)) {
            if ($user->password == md5($credentials['password'])) {
//                $user->password = Hash::make(Input::get('password'));
//                $user->save();
                Auth::login($user);
                return response()->json('Ok');
            }
        }

        return response()->json('Wrong credentials', 401);
    }

    public function user()
    {
        return response()->json(['fake' => 'user', 'user' => Auth::user()]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
    }

}
