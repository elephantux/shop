<?php

namespace Tests\Feature\App\Http\Controllers;

use App\Http\Controllers\Auth\SignInController;
use App\Http\Requests\ResetPasswordFormRequest;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
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
        $this->get(action([SignInController::class, 'index']))
            ->assertOk()
            ->assertSee('Вход в аккаунт')
            ->assertViewIs('auth.index');
    }

    /** @test */
    public function it_sign_up_page_success(): void
    {
        $this->get(action([SignInController::class, 'signUp']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.signup');
    }

    /** @test */
    public function it_forgot_page_success(): void
    {
        $this->get(action([SignInController::class, 'forgot']))
            ->assertOk()
            ->assertSee('Восстановление пароля')
            ->assertViewIs('auth.forgot-password');
    }

    /** @test */
    public function it_sign_in_success(): void
    {
        $pass = '123456789';
        $user = User::factory()->create([
            'email' => 'test@mail.ru',
            'password' => Hash::make($pass)
        ]);

        $request = SignInFormRequest::factory()->create([
            'email' => $user->email,
            'password' => $pass,
        ]);

        $response = $this->post(action([SignInController::class, 'signIn']), $request);

        $response->assertValid()
            ->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function it_logout_success()
    {
        $user = User::factory()->create([
            'email' => 'test@mail.ru',
        ]);

        $this->actingAs($user)->delete(action([SignInController::class, 'logout']));
        $this->assertGuest();
    }

    /** @test */
    public function it_reset_success()
    {
        Event::fake();
        $user = User::factory()->create([
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

        $reposnse = $this->post(action([SignInController::class, 'resetPassword']), $request);
        Event::assertDispatched(PasswordReset::class);

        $reposnse->assertValid()
            ->assertRedirect(route('login'));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    /** @test */
    public function it_store_succes(): void
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
            action([SignInController::class, 'store']),
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
