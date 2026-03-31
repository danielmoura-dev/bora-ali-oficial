<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    private function makeEventWithTicket(array $batchOverrides = []): array
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'user_id' => $user->id,
            'status' => 'published',
        ]);
        $type = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create(array_merge(
            ['ticket_type_id' => $type->id],
            $batchOverrides
        ));

        return compact('user', 'event', 'type', 'batch');
    }

    #[Test]
    public function user_can_initiate_order(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [
                    ['batch_id' => $batch->id, 'quantity' => 2],
                ],
            ])
            ->assertRedirect();

        $order = Order::where('user_id', $buyer->id)->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals(2, $order->items->first()->quantity);
    }

    #[Test]
    public function order_calculates_platform_fee_correctly(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket([
            'price' => 5000,
        ]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [
                    ['batch_id' => $batch->id, 'quantity' => 2],
                ],
            ]);

        $order = Order::where('user_id', $buyer->id)->first();

        // 2 ingressos × R$50 = R$100 subtotal
        $this->assertEquals(10000, $order->subtotal);
        // R$1,00 por item × 2 = R$2,00 de taxa
        $this->assertEquals(200, $order->platform_fee);
        // Total = R$102,00
        $this->assertEquals(10200, $order->total);
    }

    #[Test]
    public function order_fails_when_batch_is_sold_out(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket([
            'quantity' => 10,
            'quantity_sold' => 10,
        ]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [
                    ['batch_id' => $batch->id, 'quantity' => 1],
                ],
            ])
            ->assertSessionHasErrors('items');
    }

    #[Test]
    public function order_fails_when_quantity_exceeds_availability(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket([
            'quantity' => 5,
            'quantity_sold' => 3,
        ]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [
                    ['batch_id' => $batch->id, 'quantity' => 3],
                ],
            ])
            ->assertSessionHasErrors('items');
    }

    #[Test]
    public function mock_payment_confirms_order(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket();
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [['batch_id' => $batch->id, 'quantity' => 1]],
            ]);

        $order = Order::where('user_id', $buyer->id)->first();

        // Confirma o pagamento diretamente via OrderService (simula webhook)
        app(\App\Services\OrderService::class)->confirmPayment($order, 'pix');

        $this->assertEquals('paid', $order->fresh()->status);
        $this->assertNotNull($order->fresh()->items->first()->ticket_code);
    }

    #[Test]
    public function paid_order_decrements_batch_quantity(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket([
            'quantity' => 50,
        ]);
        $buyer = User::factory()->create();

        $this->actingAs($buyer)
            ->post(route('orders.store', $event->slug), [
                'items' => [['batch_id' => $batch->id, 'quantity' => 3]],
            ]);

        $order = Order::where('user_id', $buyer->id)->first();

        // Confirma via OrderService diretamente
        app(\App\Services\OrderService::class)->confirmPayment($order, 'pix');

        $this->assertEquals(3, $batch->fresh()->quantity_sold);
    }

    #[Test]
    public function user_can_see_their_tickets(): void
    {
        ['event' => $event, 'batch' => $batch] = $this->makeEventWithTicket();
        $buyer = User::factory()->create();

        Order::factory()->paid()->create([
            'user_id' => $buyer->id,
            'event_id' => $event->id,
        ]);

        $this->actingAs($buyer)
            ->get(route('tickets.my'))
            ->assertStatus(200);
    }
}