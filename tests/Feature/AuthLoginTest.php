<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_page_is_accessible(): void
    {
        $this->get(route('auth.login'))->assertStatus(200);
    }

    #[Test]
    public function user_can_login_with_correct_credentials(): void
    {
        $user = User::create([
            'name'               => 'João Silva',
            'email'              => 'joao@exemplo.com',
            'password'           => bcrypt('Senha@1234'),
            'email_verified_at'  => now(),
            'onboarding_step'    => 3,
        ]);

        $this->post(route('auth.login.store'), [
            'email'    => 'joao@exemplo.com',
            'password' => 'Senha@1234',
        ])->assertRedirect(route('home'));

        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function login_fails_with_wrong_password(): void
    {
        User::create([
            'name'     => 'João Silva',
            'email'    => 'joao@exemplo.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $this->post(route('auth.login.store'), [
            'email'    => 'joao@exemplo.com',
            'password' => 'senha-errada',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[Test]
    public function login_redirects_unverified_user_to_verify_page(): void
    {
        User::create([
            'name'     => 'João Silva',
            'email'    => 'joao@exemplo.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $this->post(route('auth.login.store'), [
            'email'    => 'joao@exemplo.com',
            'password' => 'Senha@1234',
        ])->assertRedirect(route('auth.verify.notice'));
    }

    #[Test]
    public function login_redirects_incomplete_onboarding_to_correct_step(): void
    {
        $user = User::create([
            'name'              => 'João Silva',
            'email'             => 'joao@exemplo.com',
            'password'          => bcrypt('Senha@1234'),
            'email_verified_at' => now(),
            'onboarding_step'   => 2,
        ]);

        $this->post(route('auth.login.store'), [
            'email'    => 'joao@exemplo.com',
            'password' => 'Senha@1234',
        ])->assertRedirect(route('onboarding.step2'));
    }

    #[Test]
    public function authenticated_user_can_logout(): void
    {
        $user = User::create([
            'name'     => 'João Silva',
            'email'    => 'joao@exemplo.com',
            'password' => bcrypt('Senha@1234'),
        ]);

        $this->actingAs($user)
            ->post(route('auth.logout'))
            ->assertRedirect(route('auth.login'));

        $this->assertGuest();
    }
}