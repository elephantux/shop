<?php

namespace App\Services\Telegram;

use App\Exceptions\TelegramMessageNotSentException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';

    /**
     * @throws TelegramMessageNotSentException
     */
    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        # TODO: получать json ответ и возвращать в методе boolean
        # TODO: добавить try catch и кастомный exception
        try {
            Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
            ])->throw()->json();
        } catch (RequestException $e) {
            throw new TelegramMessageNotSentException($e->getMessage(), $e->getCode());
        }

        return true;
    }
}
