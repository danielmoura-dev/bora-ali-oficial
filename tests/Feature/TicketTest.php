<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function organizer_can_add_ticket_type_to_event(): void
    {
        $user  = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('tickets.types.store', $event->slug), [
                'name'         => 'Inteira',
                'description'  => 'Ingresso inteiro',
                'is_half_price'=> false,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_types', [
            'event_id' => $event->id,
            'name'     => 'Inteira',
        ]);
    }

    #[Test]
    public function organizer_can_add_batch_to_ticket_type(): void
    {
        $user  = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $user->id]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);

        $this->actingAs($user)
            ->post(route('tickets.batches.store', [$event->slug, $type->id]), [
                'name'      => '1º Lote',
                'quantity'  => 100,
                'price'     => '50,00',
                'starts_at' => now()->subHour()->format('Y-m-d\TH:i'),
                'ends_at'   => now()->addDays(10)->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('ticket_batches', [
            'ticket_type_id' => $type->id,
            'name'           => '1º Lote',
            'price'          => 5000,
        ]);
    }

    #[Test]
    public function non_organizer_cannot_add_ticket_type(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $event = Event::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->post(route('tickets.types.store', $event->slug), [
                'name' => 'Inteira',
            ])
            ->assertStatus(403);
    }

    #[Test]
    public function ticket_type_shows_active_batch_price(): void
    {
        $type  = TicketType::factory()->create();
        $batch = TicketBatch::factory()->create([
            'ticket_type_id' => $type->id,
            'price'          => 5000,
            'quantity'       => 100,
            'quantity_sold'  => 0,
        ]);

        $this->assertTrue($type->isAvailable());
        $this->assertEquals(5000, $type->activeBatch()->price);
    }

    #[Test]
    public function sold_out_batch_shows_type_as_unavailable(): void
    {
        $type = TicketType::factory()->create();
        TicketBatch::factory()->soldOut()->create([
            'ticket_type_id' => $type->id,
        ]);

        $type->load('batches');
        $this->assertFalse($type->isAvailable());
    }
}