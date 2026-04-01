<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
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
            'status' => 'published',
        ]);
        $type = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create([
            'ticket_type_id' => $type->id,
            'price' => 5000,
            'quantity' => 100,
        ]);
        $buyer = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $buyer->id,
            'event_id' => $event->id,
            'subtotal' => 5000,
            'platform_fee' => 100,
            'total' => 5100,
            'status' => 'pending',
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
    public function pix_payment_returns_qrcode_data(): void
    {
        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        $mockProvider = Mockery::mock(\App\Payment\Contracts\PaymentProviderInterface::class);
        $mockProvider->shouldReceive('processPixPayment')
            ->once()
            ->andReturn([
                'status' => 'pending_pix',
                'payment_id' => 'mp_mock_123',
                'pix_qrcode' => 'base64encodedimage',
                'pix_copy_paste' => '00020126580014br.gov.bcb.pix',
            ]);

        $mockManager = Mockery::mock(\App\Payment\PaymentManager::class);
        $mockManager->shouldReceive('for')->once()->andReturn($mockProvider);

        $this->app->instance(\App\Payment\PaymentManager::class, $mockManager);

        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertStatus(200)
            ->assertViewIs('orders.pending');
    }

    #[Test]
    public function webhook_confirms_pending_pix_order(): void
    {
        ['order' => $order] = $this->makeOrder();

        $order->forceFill([
            'payment_method' => 'pix',
            'payment_id' => 'mp_mock_123',
        ])->save();

        $payload = json_encode([
            'action' => 'payment.updated',
            'data' => ['id' => 'mp_mock_123'],
        ]);

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
    }

    #[Test]
    public function failed_payment_keeps_order_as_pending(): void
    {
        ['buyer' => $buyer, 'order' => $order] = $this->makeOrder();

        $mockProvider = Mockery::mock(\App\Payment\Contracts\PaymentProviderInterface::class);
        $mockProvider->shouldReceive('processPixPayment')
            ->once()
            ->andReturn([
                'status' => 'failed',
                'message' => 'Erro ao gerar Pix.',
            ]);

        $mockManager = Mockery::mock(\App\Payment\PaymentManager::class);
        $mockManager->shouldReceive('for')->once()->andReturn($mockProvider);

        $this->app->instance(\App\Payment\PaymentManager::class, $mockManager);

        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertRedirect(route('orders.checkout', $order->reference));

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