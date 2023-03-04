<?php


namespace App\Telegram\Commands;


use Longman\TelegramBot\Entities\ServerResponse;
use PhpTelegramBot\Laravel\Telegram\Commands\GenericmessageCommand;

class GetnearestpoisCommand extends GenericmessageCommand
{
    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return $this->replyToChat($this->getMessage()->getLocation());
    }
}
