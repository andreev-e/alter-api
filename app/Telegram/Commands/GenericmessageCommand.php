<?php

namespace App\Telegram\Commands;

use App\Models\Poi;
use DB;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

/**
 * Generic message command
 */
class GenericmessageCommand extends SystemCommand
{
    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;

    protected $description = 'Handle generic message';

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        $lat = $this->getMessage()->getLocation()->getLatitude();
        $lng = $this->getMessage()->getLocation()->getLongitude();
        if ($lat && $lng) {
            $nearest = Poi::nearest($lat, $lng)->first();

            return $this->replyToChat($nearest->name);
        }
        return $this->replyToChat('Такое сообщение пока не поддерживается.
        Вышлите ваши координаты и я пришлю вам что инетересного рядом');
    }
}
