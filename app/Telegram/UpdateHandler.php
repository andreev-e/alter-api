<?php

namespace App\Telegram;

use App\Models\Location;
use App\Models\Poi;
use Illuminate\Database\Eloquent\Collection;

/**
 * Обработка апдейтов бота: /start, геолокация, поиск локации по названию.
 * Состояния и история не хранятся: бот отвечает на каждый апдейт сам по себе.
 */
class UpdateHandler
{
    private const MAX_RESULTS = 10;

    public function __construct(private readonly TelegramApi $api)
    {
    }

    /**
     * @param array<string, mixed> $update тело апдейта из webhook
     */
    public function handle(array $update): void
    {
        $message = $update['message'] ?? null;
        if (!isset($message['chat']['id'])) {
            return;
        }

        $chatId = (int) $message['chat']['id'];
        $languageCode = $message['from']['language_code'] ?? null;

        if (isset($message['location']['latitude'], $message['location']['longitude'])) {
            $nearest = Poi::nearest(
                (float) $message['location']['latitude'],
                (float) $message['location']['longitude']
            )->limit(self::MAX_RESULTS)->get();

            $this->api->sendMessage($chatId, $this->formatPois($nearest, $languageCode, true));

            return;
        }

        $text = trim((string) ($message['text'] ?? ''));

        if ($this->isStartCommand($text)) {
            $this->sendGreeting($chatId, $languageCode);

            return;
        }

        $location = $this->findLocation($text);
        if ($location) {
            $points = $location->pois()->limit(self::MAX_RESULTS)->get();
            $this->api->sendMessage($chatId, $this->formatPois($points, $languageCode));

            return;
        }

        $this->api->sendMessage($chatId, __('telegram.default_answer', locale: $languageCode));
    }

    private function isStartCommand(string $text): bool
    {
        return (bool) preg_match('~^/start(@\S+)?(\s|$)~', $text);
    }

    private function sendGreeting(int $chatId, ?string $languageCode): void
    {
        $this->api->sendMessage($chatId, __('telegram.greeting', locale: $languageCode), [
            'parse_mode' => 'markdown',
            'reply_markup' => [
                'keyboard' => [[[
                    'text' => __('telegram.buttons.share_location', locale: $languageCode),
                    'request_location' => true,
                ]]],
                'resize_keyboard' => true,
                'one_time_keyboard' => false,
                'selective' => true,
            ],
        ]);
    }

    /**
     * Поиск без учёта регистра по названию, затем по английскому названию.
     * Спецсимволы LIKE экранируются: ввод пользователя не должен работать как шаблон.
     */
    private function findLocation(string $text): ?Location
    {
        if ($text === '') {
            return null;
        }

        $pattern = '%' . addcslashes($text, '%_\\') . '%';

        return Location::query()->where('name', 'ILIKE', $pattern)->first()
            ?? Location::query()->where('name_en', 'ILIKE', $pattern)->first();
    }

    /**
     * @param Collection<int, Poi> $pois
     */
    private function formatPois(Collection $pois, ?string $languageCode, bool $withDistance = false): string
    {
        $host = $languageCode === 'ru' ? 'altertravel.ru' : 'altertravel.pro';

        $message = '';
        foreach ($pois as $poi) {
            $message .= $poi->name;
            if ($withDistance) {
                $message .= ' (' . round((float) $poi->dist, 1) . ' ' . __('telegram.km', locale: $languageCode) . ') ';
            }
            $message .= PHP_EOL . 'https://' . $host . '/poi/' . $poi->id . PHP_EOL;
        }

        return $message !== '' ? $message : __('telegram.default_answer', locale: $languageCode);
    }
}
