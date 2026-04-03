<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function existing_user_receives_password_reset_email(): void
    {
        Mail::fake();

        $user = User::factory()->create(['email' => 'usuario@exemplo.com']);

        $this->post(route('password.email'), ['email' => 'usuario@exemplo.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertSent(PasswordResetMail::class, fn ($mail) => $mail->hasTo('usuario@exemplo.com'));
    }

    #[Test]
    public function nonexistent_user_gets_same_generic_response(): void
    {
        Mail::fake();

        $this->post(route('password.email'), ['email' => 'naoexiste@exemplo.com'])
            ->assertRedirect()
            ->assertSessionHas('status');

        Mail::assertNothingQueued();
    }

    #[Test]
    public function valid_token_allows_password_reset(): void
    {
        $user  = User::factory()->create(['email' => 'usuario@exemplo.com']);
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'novaSenha@123',
            'password_confirmation' => 'novaSenha@123',
        ])->assertRedirect(route('auth.login'))
          ->assertSessionHas('status');

        $this->assertTrue(Hash::check('novaSenha@123', $user->fresh()->password));
    }

    #[Test]
    public function expired_token_is_rejected(): void
    {
        $user  = User::factory()->create(['email' => 'usuario@exemplo.com']);
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now()->subMinutes(61),
        ]);

        $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'novaSenha@123',
            'password_confirmation' => 'novaSenha@123',
        ])->assertRedirect()
          ->assertSessionHasErrors('email');
    }

    #[Test]
    public function token_is_invalidated_after_use(): void
    {
        $user  = User::factory()->create(['email' => 'usuario@exemplo.com']);
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        // Primeiro reset — deve funcionar
        $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'novaSenha@123',
            'password_confirmation' => 'novaSenha@123',
        ])->assertRedirect(route('auth.login'));

        // Segundo reset com mesmo token — deve falhar
        $this->post(route('password.update'), [
            'email'                 => $user->email,
            'token'                 => $token,
            'password'              => 'outraSenha@456',
            'password_confirmation' => 'outraSenha@456',
        ])->assertRedirect()
          ->assertSessionHasErrors('email');
    }

    #[Test]
    public function forgot_password_page_is_accessible(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    #[Test]
    public function reset_password_page_loads_with_valid_token(): void
    {
        $user  = User::factory()->create(['email' => 'usuario@exemplo.com']);
        $token = Str::random(64);

        DB::table('password_reset_tokens')->insert([
            'email'      => $user->email,
            'token'      => Hash::make($token),
            'created_at' => now(),
        ]);

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk();
    }
}
