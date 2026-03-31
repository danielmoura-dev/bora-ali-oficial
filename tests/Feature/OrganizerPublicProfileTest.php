<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrganizerPublicProfileTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function public_profile_is_accessible_by_username(): void
    {
        $user = User::factory()->create(['username' => 'joaosilva']);

        $this->get(route('organizer.public', 'joaosilva'))
            ->assertStatus(200)
            ->assertSee($user->name);
    }

    #[Test]
    public function public_profile_is_accessible_by_id(): void
    {
        $user = User::factory()->create(['username' => null]);

        $this->get(route('organizer.public.id', $user->id))
            ->assertStatus(200)
            ->assertSee($user->name);
    }

    #[Test]
    public function public_profile_returns_404_for_unknown_username(): void
    {
        $this->get(route('organizer.public', 'naoexiste'))
            ->assertStatus(404);
    }

    #[Test]
    public function public_profile_shows_published_events(): void
    {
        $user = User::factory()->create(['username' => 'organizador']);

        Event::factory()->current()->create([
            'user_id' => $user->id,
            'status'  => 'published',
            'title'   => 'Show Publicado',
        ]);

        Event::factory()->create([
            'user_id' => $user->id,
            'status'  => 'draft',
            'title'   => 'Evento Rascunho',
        ]);

        $this->get(route('organizer.public', 'organizador'))
            ->assertSee('Show Publicado')
            ->assertDontSee('Evento Rascunho');
    }

    #[Test]
    public function public_profile_shows_bio_and_links(): void
    {
        $user = User::factory()->create([
            'username'  => 'organizador',
            'bio'       => 'Produtor de eventos regionais.',
            'instagram' => 'organizador_ce',
            'website'   => 'https://meusite.com.br',
        ]);

        $this->get(route('organizer.public', 'organizador'))
            ->assertSee('Produtor de eventos regionais.')
            ->assertSee('organizador_ce')
            ->assertSee('meusite.com.br');
    }

    #[Test]
    public function user_can_update_public_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.public.update'), [
                'username'  => 'novousername',
                'bio'       => 'Minha bio atualizada.',
                'website'   => 'https://meusite.com.br',
                'instagram' => 'meu_insta',
                'whatsapp'  => '85999990000',
            ])
            ->assertRedirect(route('profile.show'));

        $updated = $user->fresh();
        $this->assertEquals('novousername', $updated->username);
        $this->assertEquals('Minha bio atualizada.', $updated->bio);
    }

    #[Test]
    public function username_must_be_unique(): void
    {
        User::factory()->create(['username' => 'existente']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.public.update'), [
                'username' => 'existente',
            ])
            ->assertSessionHasErrors('username');
    }

    #[Test]
    public function username_only_allows_letters_numbers_and_underscores(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('profile.public.update'), [
                'username' => 'nome inválido!',
            ])
            ->assertSessionHasErrors('username');
    }

    #[Test]
    public function public_profile_shows_finished_events_separately(): void
    {
        $user = User::factory()->create(['username' => 'organizador']);

        Event::factory()->finished()->create([
            'user_id' => $user->id,
            'status'  => 'published',
            'title'   => 'Evento Passado',
        ]);

        $this->get(route('organizer.public', 'organizador'))
            ->assertSee('Evento Passado');
    }
}