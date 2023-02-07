<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\Input;

class LoginController extends Controller
{
    public function authenticate(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return response()->json(Auth::getUser());
        }

        $user = User::query()->where('email', '=', $credentials['email'])->first();

        if (isset($user)) {
            if ($user->password == md5($credentials['password'])) {
//                $user->password = Hash::make(Input::get('password'));
//                $user->save();
                Auth::login($user);
                return response()->json($user);
            }
        }

        return response()->json('Wrong credentials', 401);
    }

    public function user(Request $request)
    {
        var_dump($request->toArray());
        response()->json('Ok');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

}
