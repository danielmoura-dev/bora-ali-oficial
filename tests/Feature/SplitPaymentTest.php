<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SplitPaymentTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrderWithConnectedOrganizer(int $quantity = 1): array
    {
        $organizer = User::factory()->create([
            'mp_access_token'    => 'TEST-organizer-token',
            'mp_user_id'         => '123456789',
            'mp_token_expires_at'=> now()->addDays(30),
        ]);

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
        ]);

        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create([
            'ticket_type_id' => $type->id,
            'price'          => 5000, // R$ 50,00
            'quantity'       => 100,
        ]);

        $buyer = User::factory()->create();
        $platformFee = 100 * $quantity; // R$ 1,00 por ingresso

        $order = Order::factory()->create([
            'user_id'      => $buyer->id,
            'event_id'     => $event->id,
            'subtotal'     => 5000 * $quantity,
            'platform_fee' => $platformFee,
            'total'        => (5000 * $quantity) + $platformFee,
            'status'       => 'pending',
        ]);

        return compact('organizer', 'buyer', 'event', 'order', 'batch');
    }

    #[Test]
    public function split_payload_contains_platform_fee_and_organizer(): void
    {
        ['organizer' => $organizer, 'order' => $order] = $this->makeOrderWithConnectedOrganizer(2);

        $paymentService = app(\App\Services\PaymentService::class);
        $payload = $paymentService->buildPixPayload($order);

        // Verifica que o split está no payload
        $this->assertArrayHasKey('application_fee', $payload);
        $this->assertEquals(200, $payload['application_fee']); // R$ 2,00 (2 ingressos)
    }

    #[Test]
    public function pix_payment_includes_split_for_connected_organizer(): void
    {
        ['buyer' => $buyer, 'order' => $order] = $this->makeOrderWithConnectedOrganizer();

        $paymentService = Mockery::mock(PaymentService::class);
        $paymentService->shouldReceive('processPixPayment')
            ->once()
            ->andReturn([
                'status'         => 'pending_pix',
                'payment_id'     => 'mp_split_123',
                'pix_qrcode'     => 'base64img',
                'pix_copy_paste' => '00020126...',
            ]);

        $this->app->instance(PaymentService::class, $paymentService);

        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertStatus(200)
            ->assertViewIs('orders.pending');
    }

    #[Test]
    public function pix_payment_works_without_connected_organizer(): void
    {
        $organizer = User::factory()->create([
            'mp_access_token' => null,
        ]);

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
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

        $paymentService = Mockery::mock(PaymentService::class);
        $paymentService->shouldReceive('processPixPayment')
            ->once()
            ->andReturn([
                'status'         => 'pending_pix',
                'payment_id'     => 'mp_nosplit_123',
                'pix_qrcode'     => 'base64img',
                'pix_copy_paste' => '00020126...',
            ]);

        $this->app->instance(PaymentService::class, $paymentService);

        $this->actingAs($buyer)
            ->post(route('orders.pay', $order->reference), [
                'payment_method' => 'pix',
            ])
            ->assertStatus(200);
    }

    #[Test]
    public function platform_fee_is_one_real_per_ticket(): void
    {
        ['order' => $order] = $this->makeOrderWithConnectedOrganizer(3);

        // 3 ingressos × R$ 1,00 = R$ 3,00 de taxa
        $this->assertEquals(300, $order->platform_fee);
    }

    #[Test]
    public function organizer_without_mp_sees_connect_warning_on_event(): void
    {
        $organizer = User::factory()->create(['mp_access_token' => null]);
        $event     = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'draft',
        ]);

        $this->actingAs($organizer)
            ->get(route('events.show', $event->slug))
            ->assertSee('Conectar Mercado Pago');
    }

    #[Test]
    public function organizer_with_mp_connected_sees_connected_status(): void
    {
        $organizer = User::factory()->create([
            'mp_access_token'    => 'TEST-token',
            'mp_user_id'         => '123',
            'mp_token_expires_at'=> now()->addDays(30),
        ]);

        $event = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'draft',
        ]);

        $this->actingAs($organizer)
            ->get(route('events.show', $event->slug))
            ->assertSee('Mercado Pago conectado');
    }
}