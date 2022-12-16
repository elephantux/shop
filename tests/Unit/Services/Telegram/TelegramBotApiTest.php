<?php

namespace Tests\Unit\Services\Telegram;

use Illuminate\Support\Facades\Http;
use Services\Telegram\TelegramBotApi;
use Tests\TestCase;

class TelegramBotApiTest extends TestCase
{
    /** @test */
    public function it_success_request()
    {
        Http::fake([
            TelegramBotApi::HOST . '*' => Http::response(['ok' => true]),
        ]);

        $request = TelegramBotApi::sendMessage('token', '123', 'test message');

        $this->assertTrue($request);
    }
}
