<?php

namespace Tests\Feature;

use App\Models\Checkin;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckinTest extends TestCase
{
    use RefreshDatabase;

    private function makeCheckinScenario(): array
    {
        $organizer = User::factory()->create();
        $event     = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
        ]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create(['ticket_type_id' => $type->id]);
        $buyer = User::factory()->create();
        $order = Order::factory()->paid()->create([
            'user_id'  => $buyer->id,
            'event_id' => $event->id,
        ]);
        $item = OrderItem::create([
            'order_id'        => $order->id,
            'ticket_batch_id' => $batch->id,
            'ticket_type_id'  => $type->id,
            'quantity'        => 1,
            'unit_price'      => 5000,
            'subtotal'        => 5000,
            'ticket_code'     => 'ABCD-1234',
        ]);

        return compact('organizer', 'event', 'buyer', 'order', 'item');
    }

    #[Test]
    public function checkin_page_is_accessible_for_organizer(): void
    {
        ['organizer' => $organizer, 'event' => $event] = $this->makeCheckinScenario();

        $this->actingAs($organizer)
            ->get(route('checkin.index', $event->slug))
            ->assertStatus(200);
    }

    #[Test]
    public function non_organizer_cannot_access_checkin_page(): void
    {
        ['event' => $event] = $this->makeCheckinScenario();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->get(route('checkin.index', $event->slug))
            ->assertStatus(403);
    }

    #[Test]
    public function organizer_can_checkin_valid_ticket(): void
    {
        ['organizer' => $organizer, 'event' => $event, 'item' => $item] =
            $this->makeCheckinScenario();

        $response = $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'ABCD-1234',
            ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('checkins', [
            'ticket_code' => 'ABCD-1234',
            'event_id'    => $event->id,
        ]);
    }

    #[Test]
    public function cannot_checkin_same_ticket_twice(): void
    {
        ['organizer' => $organizer, 'event' => $event, 'item' => $item] =
            $this->makeCheckinScenario();

        // Primeiro check-in
        $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'ABCD-1234',
            ]);

        // Segundo check-in — deve falhar
        $response = $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'ABCD-1234',
            ]);

        $response->assertStatus(409)
            ->assertJson(['status' => 'already_checked_in']);
    }

    #[Test]
    public function cannot_checkin_invalid_ticket_code(): void
    {
        ['organizer' => $organizer, 'event' => $event] = $this->makeCheckinScenario();

        $response = $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'XXXX-9999',
            ]);

        $response->assertStatus(404)
            ->assertJson(['status' => 'not_found']);
    }

    #[Test]
    public function cannot_checkin_ticket_from_different_event(): void
    {
        ['organizer' => $organizer, 'event' => $event] = $this->makeCheckinScenario();

        // Ingresso de outro evento
        $otherEvent = Event::factory()->create(['user_id' => $organizer->id]);
        $otherType  = TicketType::factory()->create(['event_id' => $otherEvent->id]);
        $otherBatch = TicketBatch::factory()->create(['ticket_type_id' => $otherType->id]);
        $otherOrder = Order::factory()->paid()->create([
            'user_id'  => User::factory()->create()->id,
            'event_id' => $otherEvent->id,
        ]);
        OrderItem::create([
            'order_id'        => $otherOrder->id,
            'ticket_batch_id' => $otherBatch->id,
            'ticket_type_id'  => $otherType->id,
            'quantity'        => 1,
            'unit_price'      => 5000,
            'subtotal'        => 5000,
            'ticket_code'     => 'OUTRO-9999',
        ]);

        $response = $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'OUTRO-9999',
            ]);

        $response->assertStatus(422)
            ->assertJson(['status' => 'wrong_event']);
    }

    #[Test]
    public function checkin_returns_ticket_holder_info(): void
    {
        ['organizer' => $organizer, 'event' => $event, 'buyer' => $buyer] =
            $this->makeCheckinScenario();

        $response = $this->actingAs($organizer)
            ->postJson(route('checkin.scan', $event->slug), [
                'ticket_code' => 'ABCD-1234',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('buyer.name', $buyer->name)
            ->assertJsonPath('buyer.email', $buyer->email);
    }

    #[Test]
    public function organizer_can_see_checkin_stats(): void
    {
        ['organizer' => $organizer, 'event' => $event] = $this->makeCheckinScenario();

        $this->actingAs($organizer)
            ->get(route('checkin.stats', $event->slug))
            ->assertStatus(200)
            ->assertJsonStructure(['total', 'checked_in', 'remaining', 'percentage']);
    }
}