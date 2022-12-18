<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    /** @test */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.forgot-password');
    }
}
