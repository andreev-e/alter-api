<?php

namespace App\Telegram\Commands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Entities\ServerResponse;

class StartCommand extends UserCommand
{
    /** @var string Command name */
    protected $name = 'start';
    /** @var string Command description */
    protected $description = 'Start';
    /** @var string Usage description */
    protected $usage = '/start';
    /** @var string Version */
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $languageCode = $this->getMessage()->getFrom()->getLanguageCode();

        $shareLocationButton = new KeyboardButton(
            [
                'text' => __('telegram.buttons.share_location', locale: $languageCode),
                'request_location' => true,
            ]
        );

        $keyboard = new Keyboard(
            [
                'keyboard' => [
                    [
                        $shareLocationButton->getRawData(),
                    ],
                ],
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
                'selective' => true,
            ]
        );


        return $this->replyToChat(__('telegram.greeting'), [
            'parse_mode' => 'markdown',
            'reply_markup' => $keyboard,
        ]);
    }
}
