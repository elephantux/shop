<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\SignInController;
use App\Http\Requests\SignInFormRequest;
use Database\Factories\UserFactory;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SignInControllerTest extends TestCase
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
    public function it_sign_in_success(): void
    {
        $pass = '123456789';
        $user = UserFactory::new()->create([
            'email' => 'test@offline.lv',
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
            'email' => 'test@offline.lv',
        ]);

        $this->actingAs($user)->delete(action([SignInController::class, 'logout']));
        $this->assertGuest();
    }
}
