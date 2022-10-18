<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class TelegramMessageNotSentException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json(["error" => true, "message" => $this->getMessage()]);
    }
}
