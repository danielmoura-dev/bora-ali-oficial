<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventSearchTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function search_returns_matching_events_by_title(): void
    {
        Event::factory()->current()->create(['title' => 'Festival de Jazz em Fortaleza']);
        Event::factory()->current()->create(['title' => 'Show de Rock Nacional']);

        $this->get(route('home', ['q' => 'jazz']))
            ->assertSee('Festival de Jazz em Fortaleza')
            ->assertDontSee('Show de Rock Nacional');
    }

    #[Test]
    public function search_returns_matching_events_by_city(): void
    {
        Event::factory()->current()->create([
            'title' => 'Evento A',
            'city'  => 'Fortaleza',
        ]);
        Event::factory()->current()->create([
            'title' => 'Evento B',
            'city'  => 'São Paulo',
        ]);

        $this->get(route('home', ['q' => 'Fortaleza']))
            ->assertSee('Evento A')
            ->assertDontSee('Evento B');
    }

    #[Test]
    public function search_with_empty_query_shows_all_current_events(): void
    {
        Event::factory()->current()->create(['title' => 'Evento Alpha']);
        Event::factory()->current()->create(['title' => 'Evento Beta']);

        $this->get(route('home', ['q' => '']))
            ->assertSee('Evento Alpha')
            ->assertSee('Evento Beta');
    }

    #[Test]
    public function search_shows_no_results_message_when_nothing_found(): void
    {
        Event::factory()->current()->create(['title' => 'Show de Forró']);

        $this->get(route('home', ['q' => 'ópera']))
            ->assertSee('Nenhum evento');
    }

    #[Test]
    public function search_is_case_insensitive(): void
    {
        Event::factory()->current()->create(['title' => 'Carnaval de Fortaleza']);

        $this->get(route('home', ['q' => 'carnaval']))
            ->assertSee('Carnaval de Fortaleza');
    }
}