<?php


namespace App\Telegram\Commands;


use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;

class GetnearestpoisCommand extends UserCommand
{
    public function execute(): ServerResponse
    {
        $message = $this->getMessage();
        $txt = trim($message->getText(true));
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        if($txt === "genericmessage") {
            $data = [];
            $data['chat_id'] = $chat_id;
            $data['reply_to_message_id'] = $message->message_id;
            $data['text'] = "a reply to a generic message";
            Request::sendMessage($data);
        }

        return Request::emptyResponse();
    }
}
