<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Telegram\TelegramApi::class, fn () => new \App\Telegram\TelegramApi(
            (string) config('telegram.bot.api_token'),
            (string) config('telegram.bot.api_url'),
        ));
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
