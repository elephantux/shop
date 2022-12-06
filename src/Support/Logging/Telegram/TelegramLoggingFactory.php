<?php

namespace Support\Logging\Telegram;

use Monolog\Logger;

class TelegramLoggingFactory
{
    public function __invoke(array $config): Logger
    {
        $looger = new Logger('telegram');
        $looger->pushHandler(new TelegramLoggingHandler($config));
        return $looger;
    }
}
