<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function registration_page_is_accessible(): void
    {
        $this->get(route('auth.register'))->assertStatus(200);
    }

    #[Test]
    public function user_can_register_with_valid_data(): void
    {
        $response = $this->post(route('auth.register.store'), [
            'name'                  => 'João Silva',
            'email'                 => 'joao@exemplo.com',
            'password'              => 'Senha@1234',
            'password_confirmation' => 'Senha@1234',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'joao@exemplo.com']);
        $response->assertRedirect(route('auth.verify.notice'));
    }

    #[Test]
    public function registration_generates_verification_code(): void
    {
        $this->post(route('auth.register.store'), [
            'name'                  => 'Maria Costa',
            'email'                 => 'maria@exemplo.com',
            'password'              => 'Senha@1234',
            'password_confirmation' => 'Senha@1234',
        ]);

        $user = User::where('email', 'maria@exemplo.com')->first();

        $this->assertNotNull($user->getAttributes()['verification_code']);
        $this->assertNotNull($user->verification_code_expires_at);
        $this->assertTrue($user->verification_code_expires_at->isFuture());
    }

    #[Test]
    public function registration_fails_with_duplicate_email(): void
    {
        User::create([
            'name'     => 'Existente',
            'email'    => 'joao@exemplo.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $response = $this->post(route('auth.register.store'), [
            'name'                  => 'João Outro',
            'email'                 => 'joao@exemplo.com',
            'password'              => 'Senha@1234',
            'password_confirmation' => 'Senha@1234',
        ]);

        $response->assertSessionHasErrors('email');
    }

    #[Test]
    public function registration_fails_with_weak_password(): void
    {
        $response = $this->post(route('auth.register.store'), [
            'name'                  => 'João Silva',
            'email'                 => 'joao@exemplo.com',
            'password'              => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
    }

    #[Test]
    public function user_can_verify_email_with_correct_code(): void
    {
        $user = User::create([
            'name'                         => 'João Silva',
            'email'                        => 'joao@exemplo.com',
            'password'                     => bcrypt('Senha@1234'),
            'verification_code'            => '123456',
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        $this->actingAs($user)
            ->post(route('auth.verify.submit'), ['code' => '123456'])
            ->assertRedirect(route('onboarding.step2'));

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function verification_fails_with_wrong_code(): void
    {
        $user = User::create([
            'name'                         => 'João Silva',
            'email'                        => 'joao@exemplo.com',
            'password'                     => bcrypt('Senha@1234'),
            'verification_code'            => '123456',
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        $this->actingAs($user)
            ->post(route('auth.verify.submit'), ['code' => '999999'])
            ->assertSessionHasErrors('code');
    }

    #[Test]
    public function verification_fails_with_expired_code(): void
    {
        $user = User::create([
            'name'                         => 'João Silva',
            'email'                        => 'joao@exemplo.com',
            'password'                     => bcrypt('Senha@1234'),
            'verification_code'            => '123456',
            'verification_code_expires_at' => now()->subMinutes(1),
        ]);

        $this->actingAs($user)
            ->post(route('auth.verify.submit'), ['code' => '123456'])
            ->assertSessionHasErrors('code');
    }
}