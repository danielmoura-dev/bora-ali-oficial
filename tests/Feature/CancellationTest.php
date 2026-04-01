<?php

namespace Tests\Feature;

use App\Mail\OrderCancelledMail;
use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CancellationTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(string $status = 'paid'): array
    {
        $organizer = User::factory()->create();
        $event     = Event::factory()->current()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
        ]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create([
            'ticket_type_id' => $type->id,
            'quantity'       => 100,
            'quantity_sold'  => 3,
        ]);
        $buyer = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'      => $buyer->id,
            'event_id'     => $event->id,
            'status'       => $status,
            'subtotal'     => 15000,
            'platform_fee' => 300,
            'total'        => 15300,
        ]);
        $item = OrderItem::create([
            'order_id'        => $order->id,
            'ticket_batch_id' => $batch->id,
            'ticket_type_id'  => $type->id,
            'quantity'        => 3,
            'unit_price'      => 5000,
            'subtotal'        => 15000,
            'ticket_code'     => 'ABCD-1234',
        ]);

        return compact('organizer', 'event', 'buyer', 'order', 'item', 'batch');
    }

    #[Test]
    public function buyer_can_cancel_paid_order(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder('paid');

        $this->actingAs($buyer)
            ->delete(route('orders.cancel', $order->reference))
            ->assertRedirect(route('tickets.my'));

        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    #[Test]
    public function cancellation_sends_email_to_buyer(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder('paid');

        $this->actingAs($buyer)
            ->delete(route('orders.cancel', $order->reference));

        Mail::assertQueued(OrderCancelledMail::class, function ($mail) use ($buyer) {
            return $mail->hasTo($buyer->email);
        });
    }

    #[Test]
    public function cancellation_restores_batch_quantity(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'order' => $order, 'batch' => $batch] = $this->makeOrder('paid');

        $quantityBefore = $batch->quantity_sold;

        $this->actingAs($buyer)
            ->delete(route('orders.cancel', $order->reference));

        $this->assertEquals(
            $quantityBefore - 3,
            $batch->fresh()->quantity_sold
        );
    }

    #[Test]
    public function buyer_cannot_cancel_another_users_order(): void
    {
        Mail::fake();

        ['order' => $order] = $this->makeOrder('paid');
        $other = User::factory()->create();

        $this->actingAs($other)
            ->delete(route('orders.cancel', $order->reference))
            ->assertStatus(403);

        $this->assertEquals('paid', $order->fresh()->status);
    }

    #[Test]
    public function cannot_cancel_already_cancelled_order(): void
    {
        Mail::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder('cancelled');

        $this->actingAs($buyer)
            ->delete(route('orders.cancel', $order->reference))
            ->assertSessionHasErrors('order');
    }

    #[Test]
    public function cannot_cancel_order_of_finished_event(): void
    {
        Mail::fake();

        $organizer = User::factory()->create();
        $event     = Event::factory()->finished()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
        ]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create(['ticket_type_id' => $type->id]);
        $buyer = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'  => $buyer->id,
            'event_id' => $event->id,
            'status'   => 'paid',
        ]);

        $this->actingAs($buyer)
            ->delete(route('orders.cancel', $order->reference))
            ->assertSessionHasErrors('order');

        $this->assertEquals('paid', $order->fresh()->status);
    }

    #[Test]
    public function organizer_can_cancel_event_and_all_orders(): void
    {
        Mail::fake();

        ['organizer' => $organizer, 'event' => $event, 'order' => $order] =
            $this->makeOrder('paid');

        $this->actingAs($organizer)
            ->delete(route('events.cancel', $event->slug))
            ->assertRedirect(route('events.show', $event->slug));

        $this->assertEquals('cancelled', $event->fresh()->status);
        $this->assertEquals('cancelled', $order->fresh()->status);
    }

    #[Test]
    public function non_organizer_cannot_cancel_event(): void
    {
        ['event' => $event] = $this->makeOrder();
        $other = User::factory()->create();

        $this->actingAs($other)
            ->delete(route('events.cancel', $event->slug))
            ->assertStatus(403);
    }

    #[Test]
    public function organizer_can_refund_specific_order(): void
    {
        Mail::fake();

        ['organizer' => $organizer, 'order' => $order] = $this->makeOrder('paid');

        $this->actingAs($organizer)
            ->post(route('orders.refund', $order->reference))
            ->assertRedirect();

        $this->assertEquals('refunded', $order->fresh()->status);

        Mail::assertQueued(OrderCancelledMail::class);
    }
}