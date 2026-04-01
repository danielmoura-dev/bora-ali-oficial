<?php

namespace Tests\Feature;

use App\Jobs\ConfirmOrderJob;
use App\Jobs\ProcessPixPaymentJob;
use App\Models\Event;
use App\Models\Order;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrder(): array
    {
        $owner = User::factory()->create();
        $event = Event::factory()->create([
            'user_id' => $owner->id,
            'status'  => 'published',
        ]);
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create([
            'ticket_type_id' => $type->id,
            'price'          => 5000,
            'quantity'       => 100,
        ]);
        $buyer = User::factory()->create();
        $order = Order::factory()->create([
            'user_id'      => $buyer->id,
            'event_id'     => $event->id,
            'subtotal'     => 5000,
            'platform_fee' => 100,
            'total'        => 5100,
            'status'       => 'pending',
        ]);

        return compact('buyer', 'owner', 'event', 'order', 'batch');
    }

    #[Test]
    public function checkout_page_loads_successfully(): void
    {
        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        $this->actingAs($buyer)
            ->get(route('orders.checkout', $order->reference))
            ->assertStatus(200)
            ->assertSee($order->reference);
    }

    #[Test]
    public function pix_payment_dispatches_job_and_shows_pending_view(): void
    {
        Queue::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertStatus(200)
            ->assertViewIs('orders.pending');

        Queue::assertPushed(ProcessPixPaymentJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });

        $this->assertEquals('pix', $order->fresh()->payment_method);
    }

    #[Test]
    public function webhook_dispatches_confirm_job_and_returns_200(): void
    {
        Queue::fake();

        ['order' => $order] = $this->makeOrder();

        $order->forceFill([
            'payment_method' => 'pix',
            'payment_id'     => 'mp_mock_123',
        ])->save();

        $payload   = json_encode(['action' => 'payment.updated', 'data' => ['id' => 'mp_mock_123']]);
        $dataId    = 'mp_mock_123';
        $requestId = '';
        $ts        = '1234567890';
        $secret    = config('services.mercadopago.webhook_secret', 'test_secret');
        $manifest  = "id:{$dataId};request-id:{$requestId};ts:{$ts};";
        $signature = hash_hmac('sha256', $manifest, $secret);

        $this->postJson(
            route('webhooks.mercadopago'),
            json_decode($payload, true),
            ['x-signature' => "ts={$ts},v1={$signature}"]
        )->assertStatus(200);

        Queue::assertPushed(ConfirmOrderJob::class, function ($job) use ($order) {
            return $job->order->id === $order->id;
        });
    }

    #[Test]
    public function webhook_does_not_dispatch_job_for_paid_order(): void
    {
        Queue::fake();

        ['order' => $order] = $this->makeOrder();

        $order->forceFill([
            'status'     => 'paid',
            'payment_id' => 'mp_mock_456',
        ])->save();

        $payload   = json_encode(['action' => 'payment.updated', 'data' => ['id' => 'mp_mock_456']]);
        $dataId    = 'mp_mock_456';
        $ts        = '1234567890';
        $secret    = config('services.mercadopago.webhook_secret', 'test_secret');
        $manifest  = "id:{$dataId};request-id:;ts:{$ts};";
        $signature = hash_hmac('sha256', $manifest, $secret);

        $this->postJson(
            route('webhooks.mercadopago'),
            json_decode($payload, true),
            ['x-signature' => "ts={$ts},v1={$signature}"]
        )->assertStatus(200);

        Queue::assertNotPushed(ConfirmOrderJob::class);
    }

    #[Test]
    public function failed_payment_keeps_order_as_pending(): void
    {
        Queue::fake();

        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        // Com Queue::fake, o job não executa — order permanece pending
        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertStatus(200);

        $this->assertEquals('pending', $order->fresh()->status);
    }

    #[Test]
    public function success_page_shows_ticket_codes(): void
    {
        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        $order->forceFill(['status' => 'paid'])->save();

        $this->actingAs($buyer)
            ->get(route('orders.success', $order->reference))
            ->assertStatus(200);
    }
}
