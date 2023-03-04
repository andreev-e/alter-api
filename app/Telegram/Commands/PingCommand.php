<?php


namespace App\Telegram\Commands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Request;

class PingCommand extends UserCommand
{
    protected $name = 'ping';
    protected $description = 'Пинг';
    protected $usage = '/ping';
    protected $version = '1.0.0';

    public function execute(): ServerResponse
    {
        $this->replyToChat('Pong');
    }
}
