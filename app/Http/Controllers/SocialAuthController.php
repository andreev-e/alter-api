<?php

namespace App\Http\Controllers;

use App\Models\SocialAccount;
use App\Models\User;
use Hash;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\User as SocialUser;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;

class SocialAuthController extends Controller
{
    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback(string $provider): Response
    {
        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (Throwable $e) {
            report($e);
            return $this->redirectToFront('/login?social_error=1');
        }

        $user = DB::transaction(fn() => $this->findOrCreateUser($provider, $socialUser));

        Auth::login($user, true);

        // The frontend finishes the login: it needs the XSRF-TOKEN cookie
        // that only the Sanctum csrf-cookie endpoint sets.
        return $this->redirectToFront('/login?social_login=1');
    }

    private function findOrCreateUser(string $provider, SocialUser $socialUser): User
    {
        $account = SocialAccount::query()
            ->where('provider', $provider)
            ->where('provider_id', (string)$socialUser->getId())
            ->first();

        if ($account && $account->user) {
            return $account->user;
        }

        $email = $socialUser->getEmail();
        $user = $email
            ? User::query()->where('email', $email)->first()
            : null;

        if (!$user) {
            $user = User::create([
                'username' => $this->uniqueUsername($socialUser, $provider),
                'email' => $email,
                'userlevel' => 1,
                'password' => Hash::make(Str::random(40)),
            ]);
        }

        SocialAccount::updateOrCreate(
            ['provider' => $provider, 'provider_id' => (string)$socialUser->getId()],
            ['user_id' => $user->id],
        );

        return $user;
    }

    private function uniqueUsername(SocialUser $socialUser, string $provider): string
    {
        $base = $socialUser->getNickname()
            ?: Str::before((string)$socialUser->getEmail(), '@')
            ?: $socialUser->getName();

        $base = Str::slug((string)$base, '_');
        if ($base === '') {
            $base = $provider . '_' . $socialUser->getId();
        }

        $username = $base;
        $i = 1;
        while (User::query()->where('username', $username)->exists()) {
            $username = $base . '_' . $i++;
        }

        return $username;
    }

    /**
     * Relative Location: the API is reached through the Nuxt proxy,
     * so the browser resolves it against the frontend domain.
     */
    private function redirectToFront(string $path): Response
    {
        return response('', 302, ['Location' => $path]);
    }
}
