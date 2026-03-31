<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\TicketBatch;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrganizerDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function makeOrganizerWithSales(): array
    {
        $organizer = User::factory()->create();

        $event1 = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
            'title'   => 'Festival Alpha',
        ]);

        $event2 = Event::factory()->create([
            'user_id' => $organizer->id,
            'status'  => 'published',
            'title'   => 'Show Beta',
        ]);

        $type1  = TicketType::factory()->create(['event_id' => $event1->id]);
        $batch1 = TicketBatch::factory()->create([
            'ticket_type_id' => $type1->id,
            'price'          => 5000,
            'quantity'       => 100,
            'quantity_sold'  => 10,
        ]);

        $type2  = TicketType::factory()->create(['event_id' => $event2->id]);
        $batch2 = TicketBatch::factory()->create([
            'ticket_type_id' => $type2->id,
            'price'          => 8000,
            'quantity'       => 50,
            'quantity_sold'  => 5,
        ]);

        // 3 pedidos pagos no event1
        $orders1 = Order::factory()->paid()->count(3)->create([
            'event_id'     => $event1->id,
            'user_id'      => User::factory()->create()->id,
            'subtotal'     => 5000,
            'platform_fee' => 100,
            'total'        => 5100,
        ]);

        // 2 pedidos pagos no event2
        $orders2 = Order::factory()->paid()->count(2)->create([
            'event_id'     => $event2->id,
            'user_id'      => User::factory()->create()->id,
            'subtotal'     => 8000,
            'platform_fee' => 100,
            'total'        => 8100,
        ]);

        return compact('organizer', 'event1', 'event2', 'batch1', 'batch2', 'orders1', 'orders2');
    }

    #[Test]
    public function dashboard_is_accessible_for_organizer(): void
    {
        $organizer = User::factory()->create();

        $this->actingAs($organizer)
            ->get(route('organizer.dashboard'))
            ->assertStatus(200);
    }

    #[Test]
    public function dashboard_shows_total_revenue(): void
    {
        ['organizer' => $organizer] = $this->makeOrganizerWithSales();

        $response = $this->actingAs($organizer)
            ->get(route('organizer.dashboard'));

        $response->assertStatus(200);
        $response->assertViewHas('totalRevenue');

        // 3 × R$51 + 2 × R$81 = R$153 + R$162 = R$315 em centavos = 31500
        $this->assertEquals(31500, $response->viewData('totalRevenue'));
    }

    #[Test]
    public function dashboard_shows_total_tickets_sold(): void
    {
        ['organizer' => $organizer, 'orders1' => $orders1] = $this->makeOrganizerWithSales();

        $response = $this->actingAs($organizer)
            ->get(route('organizer.dashboard'));

        $response->assertViewHas('totalTicketsSold');
        $this->assertGreaterThan(0, $response->viewData('totalTicketsSold'));
    }

    #[Test]
    public function dashboard_shows_only_organizer_events(): void
    {
        ['organizer' => $organizer] = $this->makeOrganizerWithSales();

        // Evento de outro organizador — não deve aparecer
        $other      = User::factory()->create();
        $otherEvent = Event::factory()->create([
            'user_id' => $other->id,
            'title'   => 'Evento de Outro',
        ]);

        $response = $this->actingAs($organizer)
            ->get(route('organizer.dashboard'));

        $response->assertDontSee('Evento de Outro');
        $response->assertSee('Festival Alpha');
    }

    #[Test]
    public function organizer_can_see_event_sales_detail(): void
    {
        ['organizer' => $organizer, 'event1' => $event] = $this->makeOrganizerWithSales();

        $this->actingAs($organizer)
            ->get(route('organizer.event.sales', $event->slug))
            ->assertStatus(200)
            ->assertSee($event->title);
    }

    #[Test]
    public function organizer_cannot_see_another_users_event_sales(): void
    {
        $organizer  = User::factory()->create();
        $other      = User::factory()->create();
        $otherEvent = Event::factory()->create(['user_id' => $other->id]);

        $this->actingAs($organizer)
            ->get(route('organizer.event.sales', $otherEvent->slug))
            ->assertStatus(403);
    }

    #[Test]
    public function dashboard_shows_pending_orders_count(): void
    {
        $organizer = User::factory()->create();
        $event     = Event::factory()->create(['user_id' => $organizer->id]);

        Order::factory()->count(2)->create([
            'event_id' => $event->id,
            'user_id'  => User::factory()->create()->id,
            'status'   => 'pending',
        ]);

        $response = $this->actingAs($organizer)
            ->get(route('organizer.dashboard'));

        $response->assertViewHas('pendingOrdersCount');
        $this->assertEquals(2, $response->viewData('pendingOrdersCount'));
    }

    #[Test]
    public function dashboard_shows_platform_fees_retained(): void
    {
        ['organizer' => $organizer] = $this->makeOrganizerWithSales();

        $response = $this->actingAs($organizer)
            ->get(route('organizer.dashboard'));

        $response->assertViewHas('totalPlatformFees');
        // 5 pedidos × R$1,00 = R$5,00 = 500 centavos
        $this->assertEquals(500, $response->viewData('totalPlatformFees'));
    }
}