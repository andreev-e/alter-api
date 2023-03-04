<?php

namespace App\Telegram\Commands;

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
    /**
     * @var string
     */
    protected $name = Telegram::GENERIC_MESSAGE_COMMAND;

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.2.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Execution if MySQL is required but not available
     *
     * @return ServerResponse
     * @throws TelegramException
     */
    public function executeNoDb(): ServerResponse
    {
        // Try to execute any deprecated system commands.
        if (self::$execute_deprecated && $deprecated_system_command_response = $this->executeDeprecatedSystemCommand()) {
            return $deprecated_system_command_response;
        }

        return Request::emptyResponse();
    }

    /**
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute(): ServerResponse
    {
        return $this->replyToChat('!!');
    }
}
