<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function google_redirect_returns_redirect_response(): void
    {
        Socialite::shouldReceive('driver->stateless->redirect')
            ->once()
            ->andReturn(redirect('https://accounts.google.com/o/oauth2/auth'));

        $this->get(route('auth.google.redirect'))
            ->assertRedirect();
    }

    #[Test]
    public function new_user_is_created_on_first_google_login(): void
    {
        $socialiteUser = $this->mockSocialiteUser(
            id: '123456789',
            name: 'Maria Google',
            email: 'maria@gmail.com',
            avatar: 'https://avatar.url/photo.jpg',
        );

        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($socialiteUser);

        $this->get(route('auth.google.callback'));

        $this->assertDatabaseHas('users', [
            'email' => 'maria@gmail.com',
            'google_id' => '123456789',
        ]);
    }

    #[Test]
    public function new_google_user_is_redirected_to_verify_notice(): void
    {
        $socialiteUser = $this->mockSocialiteUser(
            id: '123456789',
            name: 'Maria Google',
            email: 'maria@gmail.com',
            avatar: 'https://avatar.url/photo.jpg',
        );

        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($socialiteUser);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('onboarding.step2'));
    }

    #[Test]
    public function existing_google_user_is_logged_in_without_new_record(): void
    {
        $user = User::create([
            'name' => 'Maria Google',
            'email' => 'maria@gmail.com',
            'google_id' => '123456789',
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'onboarding_step' => 4,
        ]);

        $socialiteUser = $this->mockSocialiteUser(
            id: '123456789',
            name: 'Maria Google',
            email: 'maria@gmail.com',
            avatar: 'https://avatar.url/photo.jpg',
        );

        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($socialiteUser);

        $this->get(route('auth.google.callback'))
            ->assertRedirect(route('home'));

        $this->assertDatabaseCount('users', 1);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function existing_email_user_gets_google_id_linked(): void
    {
        $user = User::create([
            'name' => 'Maria Existente',
            'email' => 'maria@gmail.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $socialiteUser = $this->mockSocialiteUser(
            id: '123456789',
            name: 'Maria Google',
            email: 'maria@gmail.com',
            avatar: 'https://avatar.url/photo.jpg',
        );

        Socialite::shouldReceive('driver->stateless->user')
            ->once()
            ->andReturn($socialiteUser);

        $this->get(route('auth.google.callback'));

        $this->assertEquals('123456789', $user->fresh()->google_id);
    }

    // Helper para mockar o usuário do Socialite
    private function mockSocialiteUser(
        string $id,
        string $name,
        string $email,
        string $avatar,
    ): SocialiteUser {
        $mock = Mockery::mock(SocialiteUser::class);
        $mock->id = $id;
        $mock->name = $name;
        $mock->email = $email;
        $mock->avatar = $avatar;
        $mock->shouldReceive('getId')->andReturn($id);
        $mock->shouldReceive('getName')->andReturn($name);
        $mock->shouldReceive('getEmail')->andReturn($email);
        $mock->shouldReceive('getAvatar')->andReturn($avatar);

        return $mock;
    }
}