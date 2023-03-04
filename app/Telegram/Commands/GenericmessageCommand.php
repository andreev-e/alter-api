<?php


namespace App\Telegram\Commands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class GenericmessageCommand extends UserCommand
{
    protected $name = 'genericmessage';

    protected $description = 'Handle generic message';

    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $message = $this->getMessage();

        $message_text = $message->getText(true);

        $this->replyToChat($message_text);
    }
}
