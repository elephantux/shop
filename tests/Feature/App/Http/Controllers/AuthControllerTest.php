<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SignInController;
use App\Http\Controllers\Auth\SignUpController;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /** @test */
    public function it_login_page_success(): void
    {
        $this->get(action([SignInController::class, 'page']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.login');
    }

    /** @test */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.signup');
    }

    /** @test */
    public function it_forgot_page_success(): void
    {
        $this->get(action([ForgotPasswordController::class, 'page']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.forgot-password');
    }

    /** @test */
    public function it_sign_in_success(): void
    {
        $pass = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
            'password' => Hash::make($pass)
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $pass,
        ]);

        $response = $this->post(action([SignInController::class, 'handle']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_logout_success()
    {
        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
        ]);

        $this->actingAs($user)->delete(action([SignInController::class, 'logout']));
        $this->assertGuest();
    }

    /** @test */
    public function it_reset_success()
    {
        Event::fake();
        $user = UserFactory::new()->create([
            'email' => 'test@mail.ru',
        ]);

        $token = Password::broker()->createToken($user);
        $password = $user->password;
        $newPassword = '123123123';

        $request = ResetPasswordFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $newPassword,
            'password_confirmation' => $newPassword,
            'token' => $token,
        ]);

        $reposnse = $this->post(action([ResetPasswordController::class, 'handle']), $request);
        Event::assertDispatched(PasswordReset::class);

        $reposnse->assertValid()
            ->assertRedirect(route('login'));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /** @test */
    public function it_signup_succes(): void
    {
        Notification::fake();
        Event::fake();

        $request = SignUpFormRequest::factory()->create([
            'email' => 'slon@offline.lv',
        ]);

        $this->assertDatabaseMissing(User::class, [
            'email' => $request['email'],
        ]);

        $response = $this->post(
            action([SignUpController ::class, 'handle']),
            $request,
        );

        $response->assertValid();

        $this->assertDatabaseHas(User::class, [
            'email' => $request['email'],
        ]);

        $user = User::query()->where('email', $request['email'])->first();

        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailNewUserListener::class);

        $event = new Registered($user);
        $listener = new SendEmailNewUserListener();
        $listener->handle($event);

        Notification::assertSentTo($user, NewUserNotification::class);
        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('home'));
    }
}
