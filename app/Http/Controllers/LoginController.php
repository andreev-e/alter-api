<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

    public function user(): JsonResponse
    {
        return response()->json(Auth::user(), Auth::user() ? 200 : 401);
    }

    /**
     * The ru and en sites live on different domains with separate sessions:
     * a one-time link lets the user stay logged in when switching language.
     */
    public function transfer(Request $request): JsonResponse
    {
        $token = Str::random(64);
        Cache::put('login-transfer:' . $token, [
            'user' => Auth::id(),
            'redirect' => $this->safeRedirect($request->input('redirect')),
        ], now()->addMinutes(2));

        return response()->json(['url' => '/api/login/transfer/' . $token]);
    }

    public function finishTransfer(string $token): Response
    {
        $data = Cache::pull('login-transfer:' . $token);
        $user = $data ? User::find($data['user']) : null;

        if (!$user) {
            return response('', 302, ['Location' => '/login']);
        }

        Auth::login($user, true);

        // Relative Location: resolved against the frontend domain behind the Nuxt proxy.
        // The frontend finishes the login the same way as after a social login.
        return response('', 302, [
            'Location' => '/login?social_login=1&redirect=' . urlencode($data['redirect']),
        ]);
    }

    private function safeRedirect(?string $path): string
    {
        return $path && str_starts_with($path, '/') && !str_starts_with($path, '//')
            ? $path
            : '/';
    }

    public function logout()
    {
        Auth::logout();
    }
}
