<?php

namespace App\Telegram\Commands;

use App\Models\Poi;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ServerResponse;
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
        $location = $this->getMessage()->getLocation();
        if ($location) {
            $lat = $location->getLatitude();
            $lng = $location->getLongitude();
            $nearest = Poi::nearest($lat, $lng)->limit(3);
            $message = '';
            foreach ($nearest as $poi) {
                $message .= $poi->name . '(' . $poi->dist / 1000 . ' км) https://altertravel.ru/poi/' . $poi->id . "\n\r";
            }
            return $this->replyToChat($message);
        }
        return $this->replyToChat('Не могу помочь. Лучше пришлите ваше местоположение и я подскажу что интересного рядом');
    }
}