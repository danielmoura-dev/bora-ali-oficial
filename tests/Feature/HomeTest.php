<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function home_page_loads_for_guests(): void
    {
        $this->get(route('home'))->assertStatus(200);
    }

    #[Test]
    public function home_page_loads_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('home'))
            ->assertStatus(200);
    }

    #[Test]
    public function home_shows_current_published_events(): void
    {
        Event::factory()->current()->create(['title' => 'Show do Momento']);
        Event::factory()->finished()->create(['title' => 'Show Antigo']);

        $response = $this->get(route('home'));

        $response->assertSee('Show do Momento');
        $response->assertSee('Show Antigo');
        $response->assertSee('Eventos encerrados');
    }

    #[Test]
    public function home_shows_finished_events_section(): void
    {
        Event::factory()->finished()->create(['title' => 'Festival Passado']);
        Event::factory()->current()->create(['title' => 'Festival Futuro']);

        $response = $this->get(route('home'));

        $response->assertSee('Festival Passado');
    }

    #[Test]
    public function home_does_not_show_draft_events(): void
    {
        Event::factory()->draft()->create(['title' => 'Evento Rascunho']);

        $this->get(route('home'))
            ->assertDontSee('Evento Rascunho');
    }

    #[Test]
    public function home_does_not_show_draft_events_anywhere(): void
    {
        Event::factory()->draft()->current()->create(['title' => 'Rascunho Futuro']);
        Event::factory()->draft()->finished()->create(['title' => 'Rascunho Passado']);

        $response = $this->get(route('home'));

        $response->assertDontSee('Rascunho Futuro');
        $response->assertDontSee('Rascunho Passado');
    }

    #[Test]
    public function home_shows_empty_state_when_no_events(): void
    {
        $this->get(route('home'))
            ->assertSee('Nenhum evento');
    }
}