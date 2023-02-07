<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\User;
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
            return response()->json('Ok');
        }

        $user = User::query()->where('email', '=', $credentials['email'])->first();

        if (isset($user)) {
            if ($user->password == md5($credentials['password'])) {
//                $user->password = Hash::make(Input::get('password'));
//                $user->save();
                Auth::loginUsingId($user->username);
                var_dump($user);
                return response()->json('Ok old');
            }
        }

        return response()->json('Wrong credentials');
    }

    public function user(Request $request)
    {
        return $request->user();
    }

}
