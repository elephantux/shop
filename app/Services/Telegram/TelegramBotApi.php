<?php

namespace App\Services\Telegram;

use App\Exceptions\TelegramMessageNotSentException;
use Illuminate\Support\Facades\Http;
use Throwable;

class TelegramBotApi
{
    public const HOST = 'https://api.telegram.org/bot';
    
    public static function sendMessage(string $token, int $chatId, string $text): bool
    {
        # TODO: получать json ответ и возвращать в методе boolean
        # TODO: добавить try catch и кастомный exception
        try {
            $response = Http::get(self::HOST . $token . '/sendMessage', [
                'chat_id' => $chatId,
                'text' => $text,
            ])->throw()->json();

            return $response['ok'] ?? false;
        } catch (Throwable $e) {
            report(new TelegramMessageNotSentException($e->getMessage()));
            return false;
        }
    }
}
