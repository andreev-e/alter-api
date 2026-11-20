<?php

namespace App\Telegram;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Тонкий клиент Telegram Bot API поверх Laravel HTTP.
 * Токен попадает только в URL запроса, в логи не пишется.
 */
class TelegramApi
{
    public function __construct(
        private readonly string $token,
        private readonly string $baseUrl = 'https://api.telegram.org',
    ) {
    }

    /**
     * @param array<string, mixed> $options parse_mode, reply_markup и т.п.
     */
    public function sendMessage(int $chatId, string $text, array $options = []): bool
    {
        $url = rtrim($this->baseUrl, '/') . '/bot' . $this->token . '/sendMessage';

        $response = Http::timeout(10)
            ->asJson()
            ->post($url, array_merge(['chat_id' => $chatId, 'text' => $text], $options));

        if ($response->failed()) {
            Log::warning('Telegram sendMessage failed', ['status' => $response->status(), 'body' => $response->body()]);

            return false;
        }

        return true;
    }
}
