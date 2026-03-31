<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function profile_page_is_accessible(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertStatus(200);
    }

    #[Test]
    public function user_can_update_name_and_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'antigo@exemplo.com',
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nome Novo',
                'email' => 'novo@exemplo.com',
            ])
            ->assertRedirect(route('auth.verify.notice'));

        $updated = $user->fresh();
        $this->assertEquals('Nome Novo', $updated->name);
        $this->assertEquals('novo@exemplo.com', $updated->email);
    }

    #[Test]
    public function user_can_update_name_keeping_same_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'mesmo@exemplo.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nome Novo',
                'email' => 'mesmo@exemplo.com',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertEquals('Nome Novo', $user->fresh()->name);
    }

    #[Test]
    public function email_change_requires_reverification(): void
    {
        $user = User::factory()->create([
            'email' => 'antigo@exemplo.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => $user->name,
                'email' => 'novo@exemplo.com',
            ]);

        $this->assertNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function same_email_does_not_require_reverification(): void
    {
        $user = User::factory()->create([
            'email' => 'mesmo@exemplo.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Novo Nome',
                'email' => 'mesmo@exemplo.com',
            ]);

        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    #[Test]
    public function user_can_update_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('SenhaAntiga@1'),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.password'), [
                'current_password' => 'SenhaAntiga@1',
                'password' => 'SenhaNova@2',
                'password_confirmation' => 'SenhaNova@2',
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertTrue(Hash::check('SenhaNova@2', $user->fresh()->password));
    }

    #[Test]
    public function password_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('SenhaAntiga@1'),
        ]);

        $this->actingAs($user)
            ->patch(route('profile.password'), [
                'current_password' => 'SenhaErrada@1',
                'password' => 'SenhaNova@2',
                'password_confirmation' => 'SenhaNova@2',
            ])
            ->assertSessionHasErrors('current_password');
    }

    #[Test]
    public function user_can_upload_avatar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg', 400, 400);

        $this->actingAs($user)
            ->patch(route('profile.avatar'), [
                'avatar' => $file,
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertNotNull($user->fresh()->avatar);
        Storage::disk('public')->assertExists($user->fresh()->avatar);
    }

    #[Test]
    public function old_avatar_is_deleted_when_uploading_new_one(): void
    {
        Storage::fake('public');

        $oldFile = UploadedFile::fake()->image('old.jpg');
        $oldPath = $oldFile->store('avatars', 'public');

        $user = User::factory()->create(['avatar' => $oldPath]);

        $newFile = UploadedFile::fake()->image('new.jpg', 400, 400);

        $this->actingAs($user)
            ->patch(route('profile.avatar'), [
                'avatar' => $newFile,
            ]);

        Storage::disk('public')->assertMissing($oldPath);
    }

    #[Test]
    public function user_can_delete_account(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Senha@1234'),
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'Senha@1234',
            ])
            ->assertRedirect(route('home'));

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertGuest();
    }

    #[Test]
    public function account_deletion_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('Senha@1234'),
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), [
                'password' => 'SenhaErrada',
            ])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    #[Test]
    public function google_user_can_update_profile_without_password(): void
    {
        $user = User::factory()->create([
            'google_id' => '123456',
            'password' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('profile.update'), [
                'name' => 'Nome Atualizado',
                'email' => $user->email,
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertEquals('Nome Atualizado', $user->fresh()->name);
    }
}