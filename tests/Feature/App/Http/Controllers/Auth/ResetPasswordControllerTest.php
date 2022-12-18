<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Requests\ResetPasswordFormRequest;
use Database\Factories\UserFactory;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
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
}
