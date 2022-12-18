<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Requests\ForgotPasswordFormRequest;
use Database\Factories\UserFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Support\Flash\Flash;
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

    /** @test */
    public function it_forgot_password_success(): void
    {
        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
        ]);

        $request = ForgotPasswordFormRequest::factory()->create([
            'email' => $user->email
        ]);

        $response = $this->post(action([ForgotPasswordController::class, 'handle']), $request);
        $response->assertValid()
            ->assertSessionHas(Flash::MESSAGE_KEY, __(Password::RESET_LINK_SENT))
            ->assertStatus(302);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
