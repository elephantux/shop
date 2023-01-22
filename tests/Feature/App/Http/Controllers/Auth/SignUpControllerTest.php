<?php

namespace Tests\Feature\App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\SignUpController;
use App\Http\Requests\SignUpFormRequest;
use App\Listeners\SendEmailNewUserListener;
use App\Notifications\NewUserNotification;
use Database\Factories\UserFactory;
use Domain\Auth\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class SignUpControllerTest extends TestCase
{

    protected array $request;

    protected function setUp(): void
    {
        parent::setUp();

        $this->request = SignUpFormRequest::factory()->create([
            'email' => 'test@offline.lv',
            'password' => '123123123123',
            'password_confirmation' => '123123123123'
        ]);
    }

    private function makePostRequest(): TestResponse
    {
        return $this->post(
            action([SignUpController::class, 'handle']),
            $this->request,
        );
    }

    private function findUser(): User
    {
        return User::where('email', $this->request['email'])->first();
    }

    /** @test */
    public function it_page_success(): void
    {
        $this->get(action([SignUpController::class, 'page']))
            ->assertOk()
            ->assertSee('Регистрация')
            ->assertViewIs('auth.signup');
    }

    /** @test */
    public function it_validation_success()
    {
        $this->makePostRequest()->assertValid();
    }

    /** @test */
    public function it_should_fail_validation_on_password_confirm()
    {
        $this->request['password'] = '123';
        $this->request['password_confirmation'] = '123';

        $this->makePostRequest()->assertInvalid(['password']);
    }

    /** @test */
    public function it_user_created_success(): void
    {
        $this->assertDatabaseMissing('users', [
            'email' => $this->request['email']
        ]);

        $this->makePostRequest();

        $this->assertDatabaseHas('users', [
            'email' => $this->request['email']
        ]);
    }

    /** @test */
    public function it_should_fail_validation_on_unique_email(): void
    {
        UserFactory::new()->create([
            'email' => $this->request['email']
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $this->request['email']
        ]);

        $this->makePostRequest()->assertInvalid(['email']);
    }

    /** @test */
    public function it_registered_event_and_listeners_dispatched(): void
    {
        Event::fake();

        $this->makePostRequest();

        Event::assertDispatched(Registered::class);
        Event::assertListening(
            Registered::class,
            SendEmailNewUserListener::class
        );
    }

    /** @test */
    public function it_notification_sent(): void
    {
        $this->makePostRequest();

        Notification::assertSentTo(
            $this->findUser(),
            NewUserNotification::class
        );
    }

    /** @test */
    public function it_user_authenticated_after_and_redirected(): void
    {
        $this->makePostRequest()->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($this->findUser());
    }
}
