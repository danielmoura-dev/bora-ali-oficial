<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use App\Payment\PaymentManager;
use App\Payment\Providers\MercadoPagoProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentProviderTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(array $overrides = []): Event
    {
        $organizer = User::factory()->create([
            'mp_access_token'    => 'TEST-token',
            'mp_user_id'         => '123456',
            'mp_token_expires_at'=> now()->addDays(30),
        ]);

        return Event::factory()->create(array_merge([
            'user_id'          => $organizer->id,
            'status'           => 'published',
            'payment_provider' => 'mercadopago',
            'payment_mode'     => 'direct',
            'payment_methods'  => ['pix'],
        ], $overrides));
    }

    private function makeOrder(Event $event): Order
    {
        $type  = TicketType::factory()->create(['event_id' => $event->id]);
        $batch = TicketBatch::factory()->create(['ticket_type_id' => $type->id, 'price' => 5000]);
        $buyer = User::factory()->create();

        return Order::factory()->create([
            'user_id'      => $buyer->id,
            'event_id'     => $event->id,
            'subtotal'     => 5000,
            'platform_fee' => 100,
            'total'        => 5100,
            'status'       => 'pending',
        ]);
    }

    #[Test]
    public function payment_manager_returns_mercadopago_provider(): void
    {
        $event    = $this->makeEvent(['payment_provider' => 'mercadopago']);
        $manager  = app(PaymentManager::class);
        $provider = $manager->for($event);

        $this->assertInstanceOf(MercadoPagoProvider::class, $provider);
    }

    #[Test]
    public function direct_mode_payload_has_no_split(): void
    {
        $event   = $this->makeEvent(['payment_mode' => 'direct']);
        $order   = $this->makeOrder($event);
        $order->load(['user', 'event.organizer', 'items']);

        $provider = app(PaymentManager::class)->for($event);
        $payload  = $provider->buildPixPayload($order);

        $this->assertArrayNotHasKey('application_fee', $payload);
    }

    #[Test]
    public function split_mode_payload_has_platform_fee(): void
    {
        $event = $this->makeEvent(['payment_mode' => 'split']);
        $order = $this->makeOrder($event);
        $order->load(['user', 'event.organizer', 'items']);

        $provider = app(PaymentManager::class)->for($event);
        $payload  = $provider->buildPixPayload($order);

        $this->assertArrayHasKey('application_fee', $payload);
        $this->assertEquals(100, $payload['application_fee']);
    }

    #[Test]
    public function event_with_direct_mode_does_not_require_mp_connect(): void
    {
        $event = $this->makeEvent(['payment_mode' => 'direct']);

        $this->assertFalse($event->requiresMpConnect());
    }

    #[Test]
    public function event_with_split_mode_requires_mp_connect(): void
    {
        $event = $this->makeEvent(['payment_mode' => 'split']);

        $this->assertTrue($event->requiresMpConnect());
    }

    #[Test]
    public function event_stores_payment_configuration(): void
    {
        $event = $this->makeEvent([
            'payment_provider' => 'mercadopago',
            'payment_mode'     => 'split',
            'payment_methods'  => ['pix'],
        ]);

        $this->assertEquals('mercadopago', $event->payment_provider);
        $this->assertEquals('split', $event->payment_mode);
        $this->assertTrue($event->acceptsPix());
        $this->assertTrue($event->usesSplit());
    }

    #[Test]
    public function create_event_saves_payment_configuration(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('events.store'), [
                'title'            => 'Evento Teste',
                'description'      => str_repeat('Descricao do evento. ', 5),
                'venue_name'       => 'Local Teste',
                'venue_address'    => 'Rua Teste, 123',
                'city'             => 'Fortaleza',
                'state'            => 'CE',
                'starts_at'        => now()->addDays(10)->format('Y-m-d\TH:i'),
                'ends_at'          => now()->addDays(10)->addHours(4)->format('Y-m-d\TH:i'),
                'is_free'          => false,
                'payment_provider' => 'mercadopago',
                'payment_mode'     => 'direct',
                'payment_methods'  => ['pix'],
            ]);

        $event = Event::where('title', 'Evento Teste')->first();

        $this->assertEquals('mercadopago', $event->payment_provider);
        $this->assertEquals('direct', $event->payment_mode);
    }
}