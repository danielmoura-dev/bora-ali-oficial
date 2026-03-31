<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EventCreateTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(): User
    {
        return User::factory()->create();
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Festival de Verão',
            'description' => str_repeat('Descrição incrível do evento. ', 5),
            'venue_name' => 'Arena Castelão',
            'venue_address' => 'Av. Alberto Craveiro, 2901',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'starts_at' => now()->addDays(10)->format('Y-m-d\TH:i'),
            'ends_at' => now()->addDays(10)->addHours(6)->format('Y-m-d\TH:i'),
            'is_free' => false,
            'payment_provider' => 'mercadopago',
            'payment_mode' => 'direct',
            'payment_methods' => ['pix'],
        ], $overrides);
    }

    #[Test]
    public function create_event_page_is_accessible_for_authenticated_users(): void
    {
        $this->actingAs($this->makeUser())
            ->get(route('events.create'))
            ->assertStatus(200);
    }

    #[Test]
    public function guests_cannot_access_create_event_page(): void
    {
        $this->get(route('events.create'))
            ->assertRedirect(route('auth.login'));
    }

    #[Test]
    public function user_can_create_an_event(): void
    {
        $user = $this->makeUser();

        $response = $this->actingAs($user)
            ->post(route('events.store'), $this->validPayload());

        $event = Event::where('title', 'Festival de Verão')->first();

        $this->assertNotNull($event);
        $response->assertRedirect(route('events.show', $event->slug));
        $this->assertEquals($user->id, $event->user_id);
        $this->assertEquals('draft', $event->status);
    }

    #[Test]
    public function event_slug_is_generated_automatically(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('events.store'), $this->validPayload([
                'title' => 'Meu Evento Especial',
            ]));

        $event = Event::where('title', 'Meu Evento Especial')->first();

        $this->assertNotNull($event->slug);
        $this->assertStringContainsString('meu-evento-especial', $event->slug);
    }

    #[Test]
    public function event_creation_requires_title(): void
    {
        $this->actingAs($this->makeUser())
            ->post(route('events.store'), $this->validPayload(['title' => '']))
            ->assertSessionHasErrors('title');
    }

    #[Test]
    public function event_creation_requires_future_start_date(): void
    {
        $this->actingAs($this->makeUser())
            ->post(route('events.store'), $this->validPayload([
                'starts_at' => now()->subDay()->format('Y-m-d\TH:i'),
            ]))
            ->assertSessionHasErrors('starts_at');
    }

    #[Test]
    public function event_end_date_must_be_after_start_date(): void
    {
        $this->actingAs($this->makeUser())
            ->post(route('events.store'), $this->validPayload([
                'starts_at' => now()->addDays(10)->format('Y-m-d\TH:i'),
                'ends_at' => now()->addDays(9)->format('Y-m-d\TH:i'),
            ]))
            ->assertSessionHasErrors('ends_at');
    }

    #[Test]
    public function event_creation_requires_valid_state(): void
    {
        $this->actingAs($this->makeUser())
            ->post(route('events.store'), $this->validPayload(['state' => 'XX']))
            ->assertSessionHasErrors('state');
    }

    #[Test]
    public function user_can_upload_cover_image(): void
    {
        Storage::fake('public');

        $user = $this->makeUser();
        $file = UploadedFile::fake()->image('cover.jpg', 1200, 600);

        $this->actingAs($user)
            ->post(route('events.store'), $this->validPayload([
                'cover_image' => $file,
            ]));

        $event = Event::where('title', 'Festival de Verão')->first();

        $this->assertNotNull($event->cover_image);
        Storage::disk('public')->assertExists($event->cover_image);
    }

    #[Test]
    public function user_can_publish_their_own_event(): void
    {
        $user = $this->makeUser();
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'status' => 'draft',
        ]);

        $this->actingAs($user)
            ->patch(route('events.publish', $event->slug))
            ->assertRedirect(route('events.show', $event->slug));

        $this->assertEquals('published', $event->fresh()->status);
    }

    #[Test]
    public function user_cannot_publish_another_users_event(): void
    {
        $owner = $this->makeUser();
        $other = User::factory()->create();

        $event = Event::factory()->create([
            'user_id' => $owner->id,
            'status' => 'draft',
        ]);

        $this->actingAs($other)
            ->patch(route('events.publish', $event->slug))
            ->assertStatus(403);
    }

    #[Test]
    public function organizer_can_see_their_events_list(): void
    {
        $user = $this->makeUser();

        Event::factory()->count(3)->create(['user_id' => $user->id]);
        Event::factory()->count(2)->create(); // de outros usuários

        $response = $this->actingAs($user)
            ->get(route('events.my'));

        $response->assertStatus(200);
        $this->assertCount(3, $response->viewData('events'));
    }
}